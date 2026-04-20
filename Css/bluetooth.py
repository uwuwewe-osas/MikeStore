#!/usr/bin/env python3
# =============================================================================
# bluetooth_scanner_windows.py
# Escáner Bluetooth continuo para Windows (Clásico + BLE)
# Funciona hasta que el usuario presione Ctrl+C o cierre la ventana
# =============================================================================

import asyncio
import sys
import time
import argparse
import platform
from datetime import datetime

# ---------------------------------------------------------------------------
# Verificación de sistema operativo
# ---------------------------------------------------------------------------
if platform.system() != "Windows":
    print("AVISO: Este script está optimizado para Windows.")
    print("Para Linux/macOS usa la versión original.")
    input("Presiona Enter para continuar de todas formas...")

# ---------------------------------------------------------------------------
# Detección de dependencias
# ---------------------------------------------------------------------------
try:
    import bluetooth
    CLASSIC_AVAILABLE = True
except ImportError:
    CLASSIC_AVAILABLE = False

try:
    from bleak import BleakScanner
    BLE_AVAILABLE = True
except ImportError:
    BLE_AVAILABLE = False

# ---------------------------------------------------------------------------
# Colores para Windows (colorama) con fallback sin colores
# ---------------------------------------------------------------------------
try:
    from colorama import init, Fore, Style
    init(autoreset=True)  # Necesario en Windows para activar colores ANSI

    class C:
        RST  = Style.RESET_ALL
        BOLD = Style.BRIGHT
        RED  = Fore.LIGHTRED_EX
        GRN  = Fore.LIGHTGREEN_EX
        YLW  = Fore.LIGHTYELLOW_EX
        BLU  = Fore.LIGHTBLUE_EX
        MGT  = Fore.LIGHTMAGENTA_EX
        CYN  = Fore.LIGHTCYAN_EX
        GRY  = Fore.WHITE

    COLORS_OK = True
except ImportError:
    class C:
        RST = BOLD = RED = GRN = YLW = BLU = MGT = CYN = GRY = ""
    COLORS_OK = False

def col(text, color):
    return f"{color}{text}{C.RST}"

# ---------------------------------------------------------------------------
# Estimación de distancia por RSSI
#   Fórmula: d = 10 ^ ((TxPower − RSSI) / (10 × n))
#   n = 2.2  →  entorno mixto (interior/exterior)
#   AVISO: estimación aproximada, no usar para precisión
# ---------------------------------------------------------------------------
TX_POWER = -59   # dBm típico a 1 metro de distancia
N_FACTOR =  2.2  # exponente de pérdida de trayecto

def estimate_dist(rssi):
    if rssi is None or rssi == 0:
        return None
    return round(10 ** ((TX_POWER - rssi) / (10.0 * N_FACTOR)), 2)

def rssi_bar(rssi, width=12):
    """Barra visual de intensidad de señal."""
    if rssi is None:
        return "?" * width
    clamped = max(-100, min(-30, rssi))
    filled  = int((clamped + 100) / 70 * width)
    bar = "█" * filled + "░" * (width - filled)
    if   rssi >= -60: return col(bar, C.GRN)
    elif rssi >= -75: return col(bar, C.YLW)
    else:             return col(bar, C.RED)

def signal_label(rssi):
    if   rssi >= -50: return col("Excelente", C.GRN)
    elif rssi >= -60: return col("Buena",     C.GRN)
    elif rssi >= -70: return col("Moderada",  C.YLW)
    elif rssi >= -80: return col("Débil",     C.YLW)
    else:             return col("Muy débil", C.RED)

# ---------------------------------------------------------------------------
# Almacén de dispositivos conocidos (persiste entre ciclos)
# ---------------------------------------------------------------------------
# Clave: dirección MAC  →  Valor: dict con datos del dispositivo
known_devices: dict = {}

def update_device(data: dict):
    """Actualiza o agrega un dispositivo al almacén global."""
    addr = data["address"]
    if addr in known_devices:
        # Actualizar solo campos con datos nuevos
        if data.get("rssi") is not None:
            known_devices[addr]["rssi"] = data["rssi"]
        if data.get("name") and data["name"] != "Desconocido":
            known_devices[addr]["name"] = data["name"]
        known_devices[addr]["last_seen"] = datetime.now()
        known_devices[addr]["seen_count"] += 1
    else:
        data["first_seen"]  = datetime.now()
        data["last_seen"]   = datetime.now()
        data["seen_count"]  = 1
        known_devices[addr] = data

# ---------------------------------------------------------------------------
# Escaneo Bluetooth Clásico (PyBluez2)
# ---------------------------------------------------------------------------
def scan_classic(duration=8):
    if not CLASSIC_AVAILABLE:
        return []
    results = []
    try:
        nearby = bluetooth.discover_devices(
            duration=duration,
            lookup_names=True,
            flush_cache=True,
            lookup_class=True
        )
        for addr, name, dev_class in nearby:
            results.append({
                "name":    name or "Desconocido",
                "address": addr,
                "rssi":    None,   # PyBluez no expone RSSI en Windows
                "type":    "CLASICO",
                "class":   dev_class
            })
    except Exception as e:
        print(col(f"  [ERROR Clásico] {e}", C.RED))
    return results

# ---------------------------------------------------------------------------
# Escaneo BLE (bleak — asíncrono)
# ---------------------------------------------------------------------------
async def scan_ble_continuous(stop_event: asyncio.Event, interval: float = 10.0):
    """
    Escanea BLE en bucle continuo hasta que stop_event sea activado.
    Cada ciclo dura 'interval' segundos.
    """
    while not stop_event.is_set():
        seen_this_cycle = {}

        def on_detect(device, adv):
            if device.address not in seen_this_cycle:
                seen_this_cycle[device.address] = {
                    "name":     device.name or adv.local_name or "Desconocido",
                    "address":  device.address,
                    "rssi":     adv.rssi,
                    "tx_power": adv.tx_power,
                    "services": list(adv.service_uuids or []),
                    "type":     "BLE"
                }

        try:
            scanner = BleakScanner(detection_callback=on_detect)
            await scanner.start()
            await asyncio.sleep(interval)
            await scanner.stop()

            # Actualizar almacén global
            new_count = 0
            for data in seen_this_cycle.values():
                is_new = data["address"] not in known_devices
                update_device(data)
                if is_new:
                    new_count += 1

            if seen_this_cycle:
                print(col(
                    f"\n  [BLE] {len(seen_this_cycle)} dispositivo(s) "
                    f"en este ciclo ({new_count} nuevo(s))",
                    C.MGT
                ))

        except Exception as e:
            print(col(f"  [ERROR BLE] {e}", C.RED))
            await asyncio.sleep(3)  # Pausa antes de reintentar

# ---------------------------------------------------------------------------
# Escaneo Clásico en bucle (corre en hilo aparte via asyncio)
# ---------------------------------------------------------------------------
async def scan_classic_continuous(stop_event: asyncio.Event, interval: float = 12.0):
    """
    Escanea BT Clásico en bucle hasta que stop_event sea activado.
    El escaneo clásico es bloqueante, se ejecuta en un executor.
    """
    loop = asyncio.get_event_loop()
    while not stop_event.is_set():
        try:
            # Ejecutar en thread pool para no bloquear el event loop
            devices = await loop.run_in_executor(
                None,
                lambda: scan_classic(duration=8)
            )
            new_count = 0
            for data in devices:
                is_new = data["address"] not in known_devices
                update_device(data)
                if is_new:
                    new_count += 1

            if devices:
                print(col(
                    f"  [CLÁSICO] {len(devices)} dispositivo(s) "
                    f"({new_count} nuevo(s))",
                    C.BLU
                ))

        except Exception as e:
            print(col(f"  [ERROR Clásico] {e}", C.RED))

        # Esperar antes del siguiente ciclo clásico
        try:
            await asyncio.wait_for(
                asyncio.shield(stop_event.wait()),
                timeout=interval
            )
        except asyncio.TimeoutError:
            pass

# ---------------------------------------------------------------------------
# Mostrar tabla de todos los dispositivos conocidos
# ---------------------------------------------------------------------------
def print_table():
    """Imprime la tabla completa actualizada de dispositivos."""
    if not known_devices:
        print(col("  Sin dispositivos encontrados aún...", C.GRY))
        return

    # Ordenar: RSSI disponible primero, luego por valor descendente
    devs = sorted(
        known_devices.values(),
        key=lambda d: d.get("rssi") or -999,
        reverse=True
    )

    SEP = col("─" * 72, C.GRY)
    print(f"\n{SEP}")
    print(col(
        f"  {'#':<4}{'TIPO':<10}{'MAC':<19}{'RSSI':>8}{'DIST':>9}"
        f"  {'BARRA':<15}NOMBRE",
        C.BOLD
    ))
    print(SEP)

    for i, d in enumerate(devs, 1):
        rssi     = d.get("rssi")
        rssi_str = f"{rssi} dBm" if rssi is not None else "N/D"
        dist     = estimate_dist(rssi)
        dist_str = f"~{dist}m" if dist else "?"
        name     = (d.get("name") or "Desconocido")[:28]
        addr     = d.get("address", "??:??:??:??:??:??")
        tp       = d.get("type", "?")
        tc       = C.MGT if tp == "BLE" else C.BLU
        bar      = rssi_bar(rssi) if rssi else col("░" * 12, C.GRY)
        seen     = d.get("seen_count", 1)

        print(
            f"  {col(str(i)+'.',  C.GRY):<4}"
            f"{col(f'{tp:<10}', tc)}"
            f"{col(addr, C.CYN):<19}"
            f"{col(rssi_str, C.YLW):>8}  "
            f"{dist_str:>8}  "
            f"{bar}  {name}"
        )
        # Línea de detalle
        detail = f"    Visto: {seen}x"
        if rssi:
            detail += f"  |  {signal_label(rssi)}"
        if d.get("tx_power") is not None:
            detail += col(f"  |  TxPower: {d['tx_power']} dBm", C.GRY)
        print(col(detail, C.GRY))

    print(SEP)
    print(col(
        f"  Total acumulado: {len(devs)} dispositivo(s)  "
        f"| Actualizado: {datetime.now():%H:%M:%S}",
        C.BOLD
    ))

def print_range_summary():
    """Muestra el dispositivo más cercano y más lejano con RSSI conocido."""
    valid = [d for d in known_devices.values() if d.get("rssi") is not None]
    if not valid:
        return
    cl = max(valid, key=lambda d: d["rssi"])
    fr = min(valid, key=lambda d: d["rssi"])
    print(col("\n  ANÁLISIS DE DISTANCIA", C.BOLD))
    print(
        f"  📡 Más cercano : {col(cl['name'], C.GRN)} ({cl['address']})"
        f"  {col(str(cl['rssi'])+' dBm', C.GRN)} ≈ {estimate_dist(cl['rssi'])}m"
    )
    print(
        f"  📡 Más lejano  : {col(fr['name'], C.RED)} ({fr['address']})"
        f"  {col(str(fr['rssi'])+' dBm', C.RED)} ≈ {estimate_dist(fr['rssi'])}m"
    )

# ---------------------------------------------------------------------------
# Tarea de refresco de pantalla
# ---------------------------------------------------------------------------
async def display_loop(stop_event: asyncio.Event, refresh: float = 15.0):
    """
    Refresca la tabla de dispositivos cada 'refresh' segundos.
    No limpia la pantalla completa para que el historial sea visible.
    """
    cycle = 0
    while not stop_event.is_set():
        await asyncio.sleep(refresh)
        if stop_event.is_set():
            break
        cycle += 1
        print(col(f"\n{'═'*72}", C.BOLD))
        print(col(f"  ACTUALIZACIÓN #{cycle}  —  {datetime.now():%Y-%m-%d %H:%M:%S}", C.BOLD + C.CYN))
        print(col(f"{'═'*72}", C.BOLD))
        print_table()
        print_range_summary()

# ---------------------------------------------------------------------------
# Programa principal
# ---------------------------------------------------------------------------
async def main():
    parser = argparse.ArgumentParser(
        description="Escáner Bluetooth continuo para Windows",
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Ejemplos:
  python bluetooth_scanner_windows.py
  python bluetooth_scanner_windows.py --no-classic
  python bluetooth_scanner_windows.py --ble-interval 15 --refresh 20
  python bluetooth_scanner_windows.py --passive
        """
    )
    parser.add_argument("--no-classic",     dest="classic", action="store_false",
                        default=True,  help="Desactivar escaneo BT Clásico")
    parser.add_argument("--no-ble",         dest="ble",     action="store_false",
                        default=True,  help="Desactivar escaneo BLE")
    parser.add_argument("--passive",        action="store_true",
                        help="BLE pasivo: no envía scan requests")
    parser.add_argument("--ble-interval",   type=float, default=10.0, metavar="S",
                        help="Duración de cada ciclo BLE en segundos (def: 10)")
    parser.add_argument("--classic-interval", type=float, default=20.0, metavar="S",
                        help="Pausa entre ciclos clásicos en segundos (def: 20)")
    parser.add_argument("--refresh",        type=float, default=15.0, metavar="S",
                        help="Segundos entre actualizaciones de pantalla (def: 15)")
    args = parser.parse_args()

    # --- Encabezado ---
    print(col("═" * 72, C.BOLD))
    print(col("  ESCÁNER BLUETOOTH CONTINUO  —  Windows", C.BOLD + C.CYN))
    print(col(f"  Iniciado: {datetime.now():%Y-%m-%d %H:%M:%S}", C.GRY))
    print(col("  Presiona Ctrl+C para detener", C.YLW))
    print(col("═" * 72, C.BOLD))

    # --- Estado de dependencias ---
    if not COLORS_OK:
        print(col("\n  [TIP] Para colores en Windows: pip install colorama\n", C.YLW))

    cs = col("OK", C.GRN) if CLASSIC_AVAILABLE \
         else col("NO disponible  →  pip install PyBluez2", C.RED)
    bs = col("OK", C.GRN) if BLE_AVAILABLE \
         else col("NO disponible  →  pip install bleak",    C.RED)
    print(f"\n  PyBluez2 (BT Clásico): {cs}")
    print(f"  bleak    (BLE):        {bs}")

    if not CLASSIC_AVAILABLE and not BLE_AVAILABLE:
        print(col(
            "\n  [ERROR] No hay librerías Bluetooth instaladas.\n"
            "  Ejecuta:  pip install bleak PyBluez2 colorama\n",
            C.RED
        ))
        input("Presiona Enter para salir...")
        sys.exit(1)

    print(col("\n  Iniciando escaneo continuo...\n", C.GRN))

    # --- Evento de parada compartido entre tareas ---
    stop_event = asyncio.Event()

    tasks = []

    # Escaneo BLE continuo
    if args.ble and BLE_AVAILABLE:
        tasks.append(asyncio.create_task(
            scan_ble_continuous(stop_event, interval=args.ble_interval)
        ))

    # Escaneo Clásico continuo
    if args.classic and CLASSIC_AVAILABLE:
        tasks.append(asyncio.create_task(
            scan_classic_continuous(stop_event, interval=args.classic_interval)
        ))

    # Tarea de refresco de pantalla
    tasks.append(asyncio.create_task(
        display_loop(stop_event, refresh=args.refresh)
    ))

    if not tasks or (not BLE_AVAILABLE and not CLASSIC_AVAILABLE):
        print(col("  Sin tareas disponibles. Saliendo.", C.RED))
        return

    try:
        # Esperar indefinidamente hasta Ctrl+C
        await asyncio.gather(*tasks)
    except (KeyboardInterrupt, asyncio.CancelledError):
        pass
    finally:
        # Señalizar a todas las tareas que paren
        stop_event.set()
        for t in tasks:
            t.cancel()
        await asyncio.gather(*tasks, return_exceptions=True)

        # Mostrar resumen final
        print(col(f"\n{'═'*72}", C.BOLD))
        print(col("  RESUMEN FINAL", C.BOLD + C.CYN))
        print(col(f"{'═'*72}", C.BOLD))
        print_table()
        print_range_summary()
        print(col("\n  Escáner detenido. ¡Hasta pronto!\n", C.YLW))


if __name__ == "__main__":
    try:
        asyncio.run(main())
    except KeyboardInterrupt:
        print(col("\n\n  Interrumpido por el usuario.", C.YLW))
        sys.exit(0)