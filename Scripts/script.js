// ============================================================
// MIKE STORE — MÓDULO DE COMANDOS DE VOZ
// Requiere: productos-data.js, shared.js
// ============================================================

// Configuración de reconocimiento de voz
const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
const SpeechGrammarList = window.SpeechGrammarList || window.webkitSpeechGrammarList;

if (SpeechRecognition) {
    const recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.lang = 'es-ES';
    recognition.interimResults = true;

    if (SpeechGrammarList) {
        const grammar = '#JSGF V1.0; grammar mike; public <mike> = mike;';
        const speechRecognitionList = new SpeechGrammarList();
        speechRecognitionList.addFromString(grammar, 1);
        recognition.grammars = speechRecognitionList;
    }

    let isListeningForKeyword = false;
    let isReadyForCommand = false;
    const synth = window.speechSynthesis;

    function speak(text) {
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'es-ES';
        synth.speak(utterance);
    }

    function agregarProductoPorVoz(numeroProducto) {
        const producto = PRODUCTOS[numeroProducto - 1];
        if (producto) {
            agregarAlCarrito(producto.id, producto.tallas ? producto.tallas[0] : null);
            speak(`Se ha agregado ${producto.nombre} al carrito`);
        } else {
            speak('Producto no encontrado');
        }
    }

    function crearAreaTranscripcion() {
        const container = document.createElement('div');
        container.id = 'voice-transcript';
        Object.assign(container.style, {
            position: 'fixed', bottom: '80px', right: '20px', width: '300px',
            maxHeight: '200px', backgroundColor: 'rgba(0,0,0,0.85)', color: 'white',
            padding: '14px', borderRadius: '12px', overflowY: 'auto', display: 'none',
            fontSize: '0.85rem', zIndex: '900', boxShadow: '0 4px 20px rgba(0,0,0,0.3)'
        });
        container.innerHTML = '<h3 style="margin-bottom:8px;font-size:0.9rem;">🎙️ Transcripción</h3>';
        const text = document.createElement('div');
        text.id = 'transcript-text';
        container.appendChild(text);
        document.body.appendChild(container);
    }

    function actualizarTranscripcion(texto, tipo = 'normal') {
        const transcriptText = document.getElementById('transcript-text');
        const container = document.getElementById('voice-transcript');
        if (!transcriptText || !container) return;

        const p = document.createElement('p');
        p.textContent = texto;
        p.style.color = tipo === 'comando' ? '#ff6b00' : 'white';
        p.style.margin = '4px 0';
        transcriptText.appendChild(p);
        container.style.display = 'block';
        container.scrollTop = container.scrollHeight;
    }

    function reiniciarReconocimiento() {
        try {
            recognition.stop();
            setTimeout(() => {
                recognition.start();
                actualizarTranscripcion('Listo para otro comando', 'comando');
                speak('Estoy lista para otro comando');
            }, 500);
        } catch (error) {
            console.error('Error reiniciando reconocimiento', error);
        }
    }

    function iniciarReconocimientoVoz() {
        try {
            recognition.start();
            actualizarTranscripcion('Sistema activado. Di "Mike"', 'comando');
            isListeningForKeyword = true;
            isReadyForCommand = false;
        } catch (error) {
            console.error('Error iniciando reconocimiento de voz', error);
        }
    }

    recognition.onresult = (event) => {
        const transcript = Array.from(event.results)
            .map(result => result[0].transcript)
            .join('');

        actualizarTranscripcion(transcript);

        const lastResult = event.results[event.results.length - 1];
        if (lastResult.isFinal) {
            const finalTranscript = lastResult[0].transcript.toLowerCase().trim();

            if (!isReadyForCommand && finalTranscript.includes('mike')) {
                isReadyForCommand = true;
                speak('¿En qué puedo ayudarte?');
                actualizarTranscripcion('Comando reconocido. Escuchando...', 'comando');
            }

            if (isReadyForCommand) {
                // Agregar producto
                const match = finalTranscript.match(/agregar.*producto\s*(\d+)/i);
                if (match) {
                    const num = parseInt(match[1]);
                    agregarProductoPorVoz(num);
                    actualizarTranscripcion(`Agregando producto ${num} al carrito`, 'comando');
                    reiniciarReconocimiento();
                    return;
                }

                // Mostrar productos
                if (finalTranscript.includes('mostrar productos')) {
                    speak('Mostrando los productos disponibles');
                    actualizarTranscripcion('Listando productos', 'comando');
                    PRODUCTOS.slice(0, 6).forEach((p, i) => {
                        speak(`Producto ${i + 1}: ${p.nombre}, precio ${p.precio} soles`);
                    });
                    reiniciarReconocimiento();
                    return;
                }

                // Ver carrito
                if (finalTranscript.includes('ver carrito')) {
                    if (carrito.length === 0) {
                        speak('El carrito está vacío');
                    } else {
                        speak('Productos en el carrito:');
                        carrito.forEach(item => {
                            speak(`${item.nombre}, cantidad: ${item.cantidad}, precio: ${(item.precio * item.cantidad).toFixed(2)} soles`);
                        });
                    }
                    actualizarTranscripcion('Revisando carrito', 'comando');
                    reiniciarReconocimiento();
                    return;
                }

                // Comprar
                if (finalTranscript.includes('comprar')) {
                    if (carrito.length === 0) {
                        speak('El carrito está vacío');
                    } else {
                        speak('Redirigiendo al checkout');
                        window.location.href = 'checkout.html';
                    }
                    reiniciarReconocimiento();
                    return;
                }

                // Buscar
                const buscarMatch = finalTranscript.match(/buscar\s+(.+)/i);
                if (buscarMatch) {
                    const query = buscarMatch[1];
                    const resultados = PRODUCTOS.filter(p =>
                        p.nombre.toLowerCase().includes(query) ||
                        p.marca.toLowerCase().includes(query)
                    );
                    if (resultados.length > 0) {
                        speak(`Encontré ${resultados.length} productos para ${query}`);
                        resultados.slice(0, 3).forEach((p, i) => {
                            speak(`${i + 1}: ${p.nombre}, ${p.precio} soles`);
                        });
                    } else {
                        speak(`No encontré productos para ${query}`);
                    }
                    reiniciarReconocimiento();
                    return;
                }

                // Detener
                if (finalTranscript.includes('detener')) {
                    speak('Comandos de voz desactivados');
                    recognition.stop();
                    actualizarTranscripcion('Sistema desactivado', 'comando');
                    isListeningForKeyword = false;
                    isReadyForCommand = false;
                    const btn = document.querySelector('.btn-voz');
                    if (btn) {
                        btn.textContent = '🎙️ Activar Voz';
                        btn.classList.remove('voz-activa');
                    }
                    return;
                }
            }
        }
    };

    recognition.onerror = (event) => {
        console.error('Error en reconocimiento de voz:', event.error);
        if (event.error !== 'no-speech') {
            actualizarTranscripcion(`Error: ${event.error}`, 'comando');
        }
    };

    function crearBotonVoz() {
        const btn = document.createElement('button');
        btn.textContent = '🎙️ Activar Voz';
        btn.classList.add('btn-voz');

        btn.addEventListener('click', () => {
            if (!isListeningForKeyword) {
                iniciarReconocimientoVoz();
                speak('Comandos de voz activados. Di "Mike" para comenzar');
                btn.textContent = '🎙️ Escuchando...';
                btn.classList.add('voz-activa');
            } else {
                recognition.stop();
                isListeningForKeyword = false;
                isReadyForCommand = false;
                btn.textContent = '🎙️ Activar Voz';
                btn.classList.remove('voz-activa');
                speak('Comandos de voz desactivados');
            }
        });

        document.body.appendChild(btn);
    }

    document.addEventListener('DOMContentLoaded', () => {
        crearBotonVoz();
        crearAreaTranscripcion();
    });
} else {
    console.log('Reconocimiento de voz no soportado en este navegador');
}