// ============================================================
// MÓDULO COMPARTIDO — MikeZapatillas
// Carrito, Wishlist, Búsqueda, Filtros, Renderizado
// ============================================================

// ==================== ESTADO GLOBAL ====================
let carrito = JSON.parse(localStorage.getItem('mikestore_carrito')) || [];
let wishlist = JSON.parse(localStorage.getItem('mikestore_wishlist')) || [];
let pedidos = JSON.parse(localStorage.getItem('mikestore_pedidos')) || [];

// ==================== CARRITO ====================
function guardarCarrito() {
    localStorage.setItem('mikestore_carrito', JSON.stringify(carrito));
    actualizarContadorCarrito();
}

function agregarAlCarrito(id, talla = null) {
    const producto = PRODUCTOS.find(p => p.id === id);
    if (!producto) return;

    if (!talla && producto.tallas && producto.tallas.length > 0) {
        // Redirigir a la página de detalle si no se seleccionó talla
        window.location.href = `producto.html?id=${id}`;
        return;
    }

    const clave = `${id}-${talla}`;
    const existente = carrito.find(item => item.id === id && item.talla === talla);

    if (existente) {
        existente.cantidad++;
    } else {
        carrito.push({
            id: producto.id,
            nombre: producto.nombre,
            precio: producto.precio,
            imagen: producto.imagen,
            talla: talla,
            cantidad: 1
        });
    }

    guardarCarrito();
    mostrarNotificacion(`${producto.nombre} agregado al carrito`);
}

function eliminarDelCarrito(id, talla) {
    carrito = carrito.filter(item => !(item.id === id && item.talla === talla));
    guardarCarrito();
    renderizarCarrito();
}

function cambiarCantidadCarrito(id, talla, delta) {
    const item = carrito.find(item => item.id === id && item.talla === talla);
    if (item) {
        item.cantidad += delta;
        if (item.cantidad <= 0) {
            eliminarDelCarrito(id, talla);
            return;
        }
    }
    guardarCarrito();
    renderizarCarrito();
}

function obtenerTotalCarrito() {
    return carrito.reduce((suma, item) => suma + item.precio * item.cantidad, 0);
}

function obtenerCantidadCarrito() {
    return carrito.reduce((suma, item) => suma + item.cantidad, 0);
}

function vaciarCarrito() {
    carrito = [];
    guardarCarrito();
}

function actualizarContadorCarrito() {
    const badges = document.querySelectorAll('.carrito-badge');
    const cantidad = obtenerCantidadCarrito();
    badges.forEach(badge => {
        badge.textContent = cantidad;
        badge.style.display = cantidad > 0 ? 'flex' : 'none';
    });
}

function renderizarCarrito() {
    const itemsContainer = document.getElementById('items-carrito');
    const totalElement = document.getElementById('total-carrito');
    if (!itemsContainer || !totalElement) return;

    if (carrito.length === 0) {
        itemsContainer.innerHTML = `
            <div class="carrito-vacio">
                <span class="carrito-vacio-icon">🛒</span>
                <p>Tu carrito está vacío</p>
                <a href="index.html" class="btn-seguir-comprando">Seguir comprando</a>
            </div>`;
        totalElement.textContent = 'S/. 0.00';
        return;
    }

    itemsContainer.innerHTML = carrito.map(item => `
        <div class="carrito-item">
            <img src="${item.imagen}" alt="${item.nombre}" class="carrito-item-img">
            <div class="carrito-item-info">
                <h4>${item.nombre}</h4>
                <p class="carrito-item-talla">Talla: ${item.talla || 'Única'}</p>
                <p class="carrito-item-precio">S/. ${item.precio.toFixed(2)}</p>
            </div>
            <div class="carrito-item-acciones">
                <div class="cantidad-control">
                    <button onclick="cambiarCantidadCarrito(${item.id}, '${item.talla}', -1)">−</button>
                    <span>${item.cantidad}</span>
                    <button onclick="cambiarCantidadCarrito(${item.id}, '${item.talla}', 1)">+</button>
                </div>
                <span class="carrito-item-subtotal">S/. ${(item.precio * item.cantidad).toFixed(2)}</span>
                <button class="btn-eliminar" onclick="eliminarDelCarrito(${item.id}, '${item.talla}')">🗑️</button>
            </div>
        </div>
    `).join('');

    totalElement.textContent = `S/. ${obtenerTotalCarrito().toFixed(2)}`;
}

// ==================== WISHLIST ====================
function guardarWishlist() {
    localStorage.setItem('mikestore_wishlist', JSON.stringify(wishlist));
}

function toggleWishlist(id) {
    const idx = wishlist.indexOf(id);
    if (idx > -1) {
        wishlist.splice(idx, 1);
        mostrarNotificacion('Eliminado de favoritos');
    } else {
        wishlist.push(id);
        mostrarNotificacion('Agregado a favoritos ❤️');
    }
    guardarWishlist();
    // Actualizar corazones en la página
    document.querySelectorAll(`.btn-wishlist[data-id="${id}"]`).forEach(btn => {
        btn.classList.toggle('active', wishlist.includes(id));
        btn.textContent = wishlist.includes(id) ? '❤️' : '🤍';
    });
}

function estaEnWishlist(id) {
    return wishlist.includes(id);
}

// ==================== NOTIFICACIONES ====================
function mostrarNotificacion(mensaje) {
    // Eliminar notificación anterior
    const prev = document.querySelector('.notificacion-toast');
    if (prev) prev.remove();

    const toast = document.createElement('div');
    toast.className = 'notificacion-toast';
    toast.innerHTML = `<span>${mensaje}</span>`;
    document.body.appendChild(toast);

    setTimeout(() => toast.classList.add('show'), 10);
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 2500);
}

// ==================== BÚSQUEDA ====================
function inicializarBusqueda() {
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    if (!searchInput || !searchResults) return;

    searchInput.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        const resultados = PRODUCTOS.filter(p =>
            p.nombre.toLowerCase().includes(query) ||
            p.marca.toLowerCase().includes(query) ||
            p.categoria.toLowerCase().includes(query)
        ).slice(0, 6);

        if (resultados.length === 0) {
            searchResults.innerHTML = '<div class="search-no-results">No se encontraron resultados</div>';
        } else {
            searchResults.innerHTML = resultados.map(p => `
                <a href="producto.html?id=${p.id}" class="search-result-item">
                    <img src="${p.imagen}" alt="${p.nombre}">
                    <div>
                        <span class="search-result-name">${p.nombre}</span>
                        <span class="search-result-price">S/. ${p.precio.toFixed(2)}</span>
                    </div>
                </a>
            `).join('');
        }
        searchResults.style.display = 'block';
    });

    // Cerrar resultados al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-container')) {
            searchResults.style.display = 'none';
        }
    });
}

// ==================== FILTROS ====================
function filtrarProductos(generoFiltro = null) {
    const filtroCategoria = [...document.querySelectorAll('input[name="filter-categoria"]:checked')]
        .map(cb => cb.value);

    const precioMin = document.getElementById('filter-precio-min');
    const precioMax = document.getElementById('filter-precio-max');

    let productosFiltrados = PRODUCTOS;

    // Filtrar por género (si estamos en una página específica)
    if (generoFiltro) {
        productosFiltrados = productosFiltrados.filter(p => p.genero === generoFiltro);
    } else {
        const filtroGenero = [...document.querySelectorAll('input[name="filter-genero"]:checked')]
            .map(cb => cb.value);
        if (filtroGenero.length > 0) {
            productosFiltrados = productosFiltrados.filter(p => filtroGenero.includes(p.genero));
        }
    }

    if (filtroCategoria.length > 0) {
        productosFiltrados = productosFiltrados.filter(p => filtroCategoria.includes(p.categoria));
    }

    if (precioMin && precioMin.value) {
        productosFiltrados = productosFiltrados.filter(p => p.precio >= parseFloat(precioMin.value));
    }
    if (precioMax && precioMax.value) {
        productosFiltrados = productosFiltrados.filter(p => p.precio <= parseFloat(precioMax.value));
    }

    return productosFiltrados;
}

// ==================== ORDENAMIENTO ====================
function ordenarProductos(productos, criterio) {
    const copia = [...productos];
    switch (criterio) {
        case 'precio-asc': return copia.sort((a, b) => a.precio - b.precio);
        case 'precio-desc': return copia.sort((a, b) => b.precio - a.precio);
        case 'nombre': return copia.sort((a, b) => a.nombre.localeCompare(b.nombre));
        case 'nuevos': return copia.sort((a, b) => (b.nuevo ? 1 : 0) - (a.nuevo ? 1 : 0));
        default: return copia;
    }
}

// ==================== RENDERIZADO DE PRODUCTOS ====================
function renderizarProductos(generoFiltro = null) {
    const container = document.getElementById('productos');
    if (!container) return;

    let productosFiltrados = filtrarProductos(generoFiltro);

    // Ordenar si hay selector
    const selectOrden = document.getElementById('sort-select');
    if (selectOrden) {
        productosFiltrados = ordenarProductos(productosFiltrados, selectOrden.value);
    }

    if (productosFiltrados.length === 0) {
        container.innerHTML = `
            <div class="no-productos">
                <span>😕</span>
                <p>No se encontraron productos con los filtros seleccionados</p>
            </div>`;
        return;
    }

    container.innerHTML = productosFiltrados.map(producto => {
        const enWishlist = estaEnWishlist(producto.id);
        const promedioResenas = producto.resenas.length > 0
            ? (producto.resenas.reduce((s, r) => s + r.puntuacion, 0) / producto.resenas.length)
            : 0;

        return `
        <div class="producto" data-id="${producto.id}">
            <div class="producto-badges">
                ${producto.nuevo ? '<span class="badge-nuevo">NUEVO</span>' : ''}
                ${producto.enOferta ? '<span class="badge-oferta">OFERTA</span>' : ''}
            </div>
            <button class="btn-wishlist ${enWishlist ? 'active' : ''}" data-id="${producto.id}" onclick="event.stopPropagation(); toggleWishlist(${producto.id})">
                ${enWishlist ? '❤️' : '🤍'}
            </button>
            <a href="producto.html?id=${producto.id}" class="producto-link">
                <img src="${producto.imagen}" alt="${producto.nombre}" loading="lazy">
            </a>
            <div class="producto-info">
                <span class="producto-marca">${producto.marca}</span>
                <h3><a href="producto.html?id=${producto.id}">${producto.nombre}</a></h3>
                <div class="producto-rating">
                    ${renderEstrellas(promedioResenas)}
                    <span class="rating-count">(${producto.resenas.length})</span>
                </div>
                <div class="producto-precios">
                    ${producto.precioOriginal ? `<span class="precio-original">S/. ${producto.precioOriginal.toFixed(2)}</span>` : ''}
                    <span class="precio ${producto.enOferta ? 'precio-oferta' : ''}">S/. ${producto.precio.toFixed(2)}</span>
                </div>
                <button class="btn-agregar" onclick="window.location.href='producto.html?id=${producto.id}'">
                    Ver Producto
                </button>
            </div>
        </div>`;
    }).join('');
}

function renderEstrellas(promedio) {
    let html = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= Math.floor(promedio)) {
            html += '<span class="estrella llena">★</span>';
        } else if (i - 0.5 <= promedio) {
            html += '<span class="estrella media">★</span>';
        } else {
            html += '<span class="estrella vacia">☆</span>';
        }
    }
    return html;
}

// ==================== NAV COMPARTIDO ====================
function generarNav() {
    return `
    <header>
        <nav>
            <a href="index.html" class="logo">Mike Store</a>
            <div class="search-container">
                <input type="text" id="search-input" placeholder="Buscar zapatillas..." autocomplete="off">
                <button class="search-btn">🔍</button>
                <div id="search-results" class="search-results"></div>
            </div>
            <button class="menu-toggle" id="menu-toggle" aria-label="Menú">
                <span></span><span></span><span></span>
            </button>
            <ul id="nav-menu">
                <li><a href="index.html">Inicio</a></li>
                <li><a href="hombres.html">Hombres</a></li>
                <li><a href="mujeres.html">Mujeres</a></li>
                <li><a href="niños.html">Niños</a></li>
                <li>
                    <a href="wishlist.html" class="nav-icon" title="Favoritos">
                        ❤️ <span class="nav-label">Favoritos</span>
                    </a>
                </li>
                <li>
                    <a href="#" class="carrito nav-icon" title="Carrito">
                        🛒 <span class="nav-label">Carrito</span>
                        <span class="carrito-badge">0</span>
                    </a>
                </li>
                <li>
                    <a href="perfil.html" class="nav-icon" title="Mi Cuenta">
                        👤 <span class="nav-label">Cuenta</span>
                    </a>
                </li>
            </ul>
        </nav>
    </header>`;
}

function generarFooter() {
    return `
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>Mike Store</h4>
                <p>Tu tienda de zapatillas favorita. Estilo y comodidad en cada paso.</p>
            </div>
            <div class="footer-section">
                <h4>Enlaces</h4>
                <ul>
                    <li><a href="index.html">Inicio</a></li>
                    <li><a href="hombres.html">Hombres</a></li>
                    <li><a href="mujeres.html">Mujeres</a></li>
                    <li><a href="niños.html">Niños</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Contacto</h4>
                <p>📧 mike.store@gmail.com</p>
                <p>📞 987 654 321</p>
            </div>
            <div class="footer-section">
                <h4>Síguenos</h4>
                <div class="footer-social">
                    <a href="https://facebook.com" target="_blank">Facebook</a>
                    <a href="https://instagram.com" target="_blank">Instagram</a>
                    <a href="https://twitter.com" target="_blank">Twitter</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 Mike Store. Todos los derechos reservados.</p>
        </div>
    </footer>`;
}

function generarModalCarrito() {
    return `
    <div id="modal-carrito" class="modal">
        <div class="modal-contenido">
            <div class="modal-header">
                <h2>Tu Carrito</h2>
                <span class="cerrar">&times;</span>
            </div>
            <div id="items-carrito"></div>
            <div class="carrito-footer">
                <div class="total">
                    <span>Total:</span>
                    <span id="total-carrito" class="total-precio">S/. 0.00</span>
                </div>
                <a href="checkout.html" class="btn-checkout" id="btn-checkout">Proceder al Pago</a>
            </div>
        </div>
    </div>`;
}

// ==================== PEDIDOS ====================
function guardarPedido(pedido) {
    pedidos.push(pedido);
    localStorage.setItem('mikestore_pedidos', JSON.stringify(pedidos));
}

// ==================== INICIALIZACIÓN ====================
function initShared() {
    // Menú hamburguesa
    const menuToggle = document.getElementById('menu-toggle');
    const navMenu = document.getElementById('nav-menu');
    if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
            menuToggle.classList.toggle('active');
        });
    }

    // Modal carrito
    const carritoModal = document.getElementById('modal-carrito');
    const cerrarModal = document.querySelector('.cerrar');
    const botonCarrito = document.querySelector('.carrito');

    if (botonCarrito && carritoModal) {
        botonCarrito.addEventListener('click', (e) => {
            e.preventDefault();
            carritoModal.style.display = 'flex';
            renderizarCarrito();
        });
    }

    if (cerrarModal && carritoModal) {
        cerrarModal.addEventListener('click', () => {
            carritoModal.style.display = 'none';
        });
    }

    if (carritoModal) {
        carritoModal.addEventListener('click', (e) => {
            if (e.target === carritoModal) {
                carritoModal.style.display = 'none';
            }
        });
    }

    // Búsqueda
    inicializarBusqueda();

    // Actualizar badge carrito
    actualizarContadorCarrito();

    // Marcar página activa en nav
    const currentPage = window.location.pathname.split('/').pop();
    document.querySelectorAll('nav ul li a').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active-link');
        }
    });

    // Ordenamiento
    const selectOrden = document.getElementById('sort-select');
    if (selectOrden) {
        selectOrden.addEventListener('change', () => {
            const genero = document.body.dataset.genero || null;
            renderizarProductos(genero);
        });
    }
}

document.addEventListener('DOMContentLoaded', initShared);
