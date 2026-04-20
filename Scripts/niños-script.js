// Datos de productos para hombres
const productos = [
    {
        id: 200,
        nombre: 'Zapatillas Puma Amare',
        precio: 139.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/3/7/376209-07_1.jpg',
        categoria: 'Correr',
        genero: 'niños'
    },
    {
        id: 201,
        nombre: 'Zapatillas Nike Court Borough Low 2',
        precio: 179.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/b/q/bq5451-104_1_1_1.jpg',
        categoria: 'Urbano',
        genero: 'niños'
    },
    {
        id: 202,
        nombre: 'Zapatillas Adidas Grand Court 2.0 EL',
        precio: 169.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/w/gw6521_1.jpg',
        categoria: 'Urbano',
        genero: 'niños'
    },
    {
        id: 203,
        nombre: 'Zapatillas Adidas Predator Accuracy.4 H&L TF J',
        precio:  179.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/w/gw7083_1.jpg',
        categoria: 'Fútbol',
        genero: 'niños'
    },
    {
        id: 204,
        nombre: 'Zapatillas Adidas Racer TR21 Woody I',
        precio: 129.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/y/gy4450_1_1.jpg',
        categoria: 'Correr',
        genero: 'niños'
    },
    {
        id: 205,
        nombre: 'Zapatillas Adidas Vulc Raid3R Muppets',
        precio: 139.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/z/gz1700_1.jpg',
        categoria: 'Urbano',
        genero: 'niños'
    },
    {
        id: 206,
        nombre: 'Zapatillas Adidas Advantage K',
        precio: 89.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/0/h06181_1.jpg',
        categoria: 'Urbano',
        genero: 'niños'
    },
    {
        id: 207,
        nombre: 'Zapatillas Adidas Vulcraid3R Spiderman CF C',
        precio: 179.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/p/hp7750_1.jpg',
        categoria: 'Urbano',
        genero: 'niños'
    },
    {
        id: 208,
        nombre: 'Zapatillas Adidas Breaknet 2.0 K',
        precio: 113.00,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/p/hp8956_1.jpg',
        categoria: 'Urbano',
        genero: 'niños'
    },
    {
        id: 209,
        nombre: 'Zapatillas Adidas Cross EM Up 5 K Wide',
        precio: 186.00,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/q/hq8494_1.jpg',
        categoria: 'Básquet',
        genero: 'niños'
    },
    {
        id: 210,
        nombre: 'Zapatillas Adidas Advantage K',
        precio: 149.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/y/gy6995_1.jpg',
        categoria: 'Urbano',
        genero: 'niños'
    },
    {
        id: 211,
        nombre: 'Zapatillas Adidas Grand Court Spider',
        precio: 159.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/i/f/if9893_1.jpg',
        categoria: 'Urbano',
        genero: 'niños'
    }
];

// Carrito de compras
let carrito = [];

// Elementos del DOM
const productosContainer = document.getElementById('productos');
const carritoModal = document.getElementById('modal-carrito');
const cerrarModal = document.querySelector('.cerrar');
const botonCarrito = document.querySelector('.carrito');
const itemsCarrito = document.getElementById('items-carrito');
const totalCarrito = document.getElementById('total-carrito');
const botonComprar = document.getElementById('comprar');

// Función para filtrar productos
function filtrarProductos() {
    const filtroGenero = [...document.querySelectorAll('input[name="filter-genero"]:checked')]
        .map(checkbox => checkbox.value);

    const filtroCategoria = [...document.querySelectorAll('input[name="filter-categoria"]:checked')]
        .map(checkbox => checkbox.value);

    const preciominimoInput = document.getElementById('filter-precio-min');
    const precioMaximoInput = document.getElementById('filter-precio-max');

    return productos.filter(producto => {
        const cumpleGenero = filtroGenero.length === 0 || filtroGenero.includes(producto.genero);
        const cumpleCategoria = filtroCategoria.length === 0 || filtroCategoria.includes(producto.categoria);
        const cumplePrecio = (!preciominimoInput.value || producto.precio >= parseFloat(preciominimoInput.value)) &&
                             (!precioMaximoInput.value || producto.precio <= parseFloat(precioMaximoInput.value));
        return cumpleGenero && cumpleCategoria && cumplePrecio;
    });
}

// Función para renderizar productos filtrados
function renderizarProductos() {
    const productosFiltrados = filtrarProductos();
    productosContainer.innerHTML = productosFiltrados.map(producto => `
        <div class="producto" data-id="${producto.id}">
            <img src="${producto.imagen}" alt="${producto.nombre}">
            <div class="producto-info">
                <h4>${producto.categoria}</h4>
                <h3>${producto.nombre}</h3>
                <p class="precio">S/. ${producto.precio.toFixed(2)}</p>
                <button class="btn-agregar" onclick="agregarAlCarrito(${producto.id})">Agregar al Carrito</button>
            </div>
        </div>
    `).join('');
}

// Resto de las funciones de carrito son idénticas al script original
function agregarAlCarrito(id) {
    const producto = productos.find(p => p.id === id);
    const existente = carrito.find(item => item.id === id);

    if (existente) {
        existente.cantidad++;
    } else {
        carrito.push({...producto, cantidad: 1});
    }

    actualizarCarrito();
}

function actualizarCarrito() {
    itemsCarrito.innerHTML = carrito.map(item => `
        <div class="carrito-item">
            <span>${item.nombre} x ${item.cantidad}</span>
            <span>$${(item.precio * item.cantidad).toFixed(2)}</span>
            <button onclick="eliminarDelCarrito(${item.id})">Eliminar</button>
        </div>
    `).join('');

    const total = carrito.reduce((suma, item) => suma + item.precio * item.cantidad, 0);
    totalCarrito.textContent = `$${total.toFixed(2)}`;
}

function eliminarDelCarrito(id) {
    const index = carrito.findIndex(item => item.id === id);
    if (index > -1) {
        carrito.splice(index, 1);
        actualizarCarrito();
    }
}

// Eventos de modal
botonCarrito.addEventListener('click', () => {
    carritoModal.style.display = 'block';
});

cerrarModal.addEventListener('click', () => {
    carritoModal.style.display = 'none';
});

botonComprar.addEventListener('click', () => {
    if (carrito.length === 0) {
        alert('Tu carrito está vacío');
        return;
    }
    alert('¡Gracias por tu compra!');
    carrito = [];
    actualizarCarrito();
    carritoModal.style.display = 'none';
});

//////////////////////////
////////////////////////
// Configuración de reconocimiento de voz
const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
const SpeechGrammarList = window.SpeechGrammarList || window.webkitSpeechGrammarList;

// Verificar soporte de reconocimiento de voz
if (!SpeechRecognition) {
    alert('Tu navegador no soporta reconocimiento de voz');
}

// Inicializar reconocimiento de voz
const recognition = new SpeechRecognition();
recognition.continuous = true;
recognition.lang = 'es-ES';
recognition.interimResults = true;

// Configurar gramática de comandos
const grammar = '#JSGF V1.0; grammar mike; public <mike> = mike;';
const speechRecognitionList = new SpeechGrammarList();
speechRecognitionList.addFromString(grammar, 1);
recognition.grammars = speechRecognitionList;

// Estado de activación de comandos de voz
let isListeningForKeyword = false;
let isReadyForCommand = false;

// Síntesis de voz para respuestas
const synth = window.speechSynthesis;

// Función para hablar
function speak(text) {
    const utterance = new SpeechSynthesisUtterance(text);
    utterance.lang = 'es-ES';
    synth.speak(utterance);
}

// Agregar productos por voz
function agregarProductoPorVoz(numeroProducto) {
    const producto = productos[numeroProducto - 1];
    if (producto) {
        agregarAlCarrito(producto.id);
        speak(`Se ha agregado ${producto.nombre} al carrito`);
    } else {
        speak('Producto no encontrado');
    }
}

// Crear área de transcripción
function crearAreaTranscripcion() {
    const transcriptContainer = document.createElement('div');
    transcriptContainer.id = 'voice-transcript';
    transcriptContainer.style.position = 'fixed';
    transcriptContainer.style.bottom = '80px';
    transcriptContainer.style.right = '20px';
    transcriptContainer.style.width = '300px';
    transcriptContainer.style.height = '200px';
    transcriptContainer.style.backgroundColor = 'rgba(0,0,0,0.8)';
    transcriptContainer.style.color = 'white';
    transcriptContainer.style.padding = '10px';
    transcriptContainer.style.borderRadius = '10px';
    transcriptContainer.style.overflowY = 'auto';
    transcriptContainer.style.display = 'none';
    transcriptContainer.innerHTML = '<h3>Transcripción de Voz</h3>';

    const transcriptText = document.createElement('div');
    transcriptText.id = 'transcript-text';
    transcriptContainer.appendChild(transcriptText);

    document.body.appendChild(transcriptContainer);
}

// Actualizar transcripción
function actualizarTranscripcion(texto, tipo = 'normal') {
    const transcriptText = document.getElementById('transcript-text');
    const transcriptContainer = document.getElementById('voice-transcript');
    
    if (!transcriptText || !transcriptContainer) return;

    const nuevoParrafo = document.createElement('p');
    nuevoParrafo.textContent = texto;
    nuevoParrafo.style.color = tipo === 'comando' ? '#ff6b00' : 'white';
    
    transcriptText.appendChild(nuevoParrafo);
    transcriptContainer.style.display = 'block';
    
    // Desplazar al final
    transcriptContainer.scrollTop = transcriptContainer.scrollHeight;
}

// Reiniciar reconocimiento después de un comando
function reiniciarReconocimiento() {
    try {
        recognition.stop();
        setTimeout(() => {
            recognition.start();
            actualizarTranscripcion('Esperando siguiente comando', 'comando');
            speak('Estoy lista para otro comando');
        }, 500);
    } catch (error) {
        console.error('Error reiniciando reconocimiento', error);
    }
}

// Iniciar reconocimiento de voz
function iniciarReconocimientoVoz() {
    try {
        recognition.start();
        console.log('Reconocimiento de voz iniciado');
        actualizarTranscripcion('Sistema activado. Di "Mike"', 'comando');
        isListeningForKeyword = true;
        isReadyForCommand = false;
    } catch (error) {
        console.error('Error iniciando reconocimiento de voz', error);
    }
}

// Evento de resultado de reconocimiento
recognition.onresult = (event) => {
    const transcript = Array.from(event.results)
        .map(result => result[0].transcript)
        .join('');
    
    // Mostrar transcripción en tiempo real
    actualizarTranscripcion(transcript);

    const lastResult = event.results[event.results.length - 1];
    if (lastResult.isFinal) {
        const finalTranscript = lastResult[0].transcript.toLowerCase().trim();
        console.log('Comando recibido:', finalTranscript);

        // Lógica de palabras clave y comandos
        if (!isReadyForCommand && finalTranscript.includes('mike')) {
            isReadyForCommand = true;
            speak('¿En qué puedo ayudarte el día de hoy?');
            actualizarTranscripcion('Sistema activado. Comando reconocido', 'comando');
        } 
        
        if (isReadyForCommand) {
            // Comando para agregar productos
            const agregarProductoMatch = finalTranscript.match(/agregar.*producto\s*(\d+)/i);
            if (agregarProductoMatch) {
                const numeroProductoOriginal = parseInt(agregarProductoMatch[1]);

                // Obtener productos filtrados
                const productosFiltrados = filtrarProductos(); // Obtener la lista filtrada de productos

                // Verificar si el número del producto solicitado es válido en la lista filtrada
                if (numeroProductoOriginal > 0 && numeroProductoOriginal <= productosFiltrados.length) {
                    const productoFiltrado = productosFiltrados[numeroProductoOriginal - 1]; // Productos están indexados desde 0
                    
                    // Aquí añadimos el producto al carrito
                    const productoEnCarrito = carrito.find(item => item.id === productoFiltrado.id);
                    if (productoEnCarrito) {
                        productoEnCarrito.cantidad += 1; // Si ya está en el carrito, aumentamos la cantidad
                    } else {
                        carrito.push({...productoFiltrado, cantidad: 1}); // Si no está, lo agregamos con cantidad 1
                    }

                    actualizarCarrito(); // Actualizamos la vista del carrito

                    actualizarTranscripcion(`Agregando ${productoFiltrado.nombre} al carrito`, 'comando');
                    speak(`Producto agregado al carrito: ${productoFiltrado.nombre}`);
                    reiniciarReconocimiento();
                    return;
                } else {
                    speak('Producto no disponible en la lista filtrada');
                    actualizarTranscripcion('Producto no encontrado en la lista filtrada', 'comando');
                    reiniciarReconocimiento();
                    return;
                }
            }

            // Comando para mostrar productos
            if (finalTranscript.includes('mostrar productos')) {
                const productosFiltrados = filtrarProductos(); // Obtener la lista filtrada de productos
                speak('Los productos disponibles son:');
                actualizarTranscripcion('Mostrando lista de productos', 'comando');
                productosFiltrados.forEach((producto, index) => {
                    speak(`Producto ${index + 1}: ${producto.nombre}, precio ${producto.precio} soles`);
                });
                reiniciarReconocimiento();
                return;
            }

            // Otros comandos (ver carrito, comprar, etc.)
            if (finalTranscript.includes('ver carrito')) {
                actualizarTranscripcion('Revisando contenido del carrito', 'comando');
                if (carrito.length === 0) {
                    speak('El carrito está vacío');
                } else {
                    speak('Productos en el carrito:');
                    carrito.forEach(item => {
                        speak(`${item.nombre}, cantidad: ${item.cantidad}, precio total: ${(item.precio * item.cantidad).toFixed(2)} dólares`);
                    });
                }
                reiniciarReconocimiento();
                return;
            }

            // Comando para comprar
            if (finalTranscript.includes('comprar')) {
                actualizarTranscripcion('Procesando compra', 'comando');
                if (carrito.length === 0) {
                    speak('El carrito está vacío. No se puede realizar la compra');
                } else {
                    speak('Realizando compra. Gracias por tu compra');
                    carrito = [];
                    actualizarCarrito();
                }
                reiniciarReconocimiento();
                return;
            }

            // Comando para detener
            if (finalTranscript.includes('detener')) {
                speak('Reconocimiento de voz detenido');
                recognition.stop();
                actualizarTranscripcion('Sistema desactivado', 'comando');
                isListeningForKeyword = false;
                isReadyForCommand = false;
                return;
            }
        }
    }
};

// Manejar errores de reconocimiento
recognition.onerror = (event) => {
    console.error('Error en reconocimiento de voz:', event.error);
    speak('Hubo un error en el reconocimiento de voz');
    actualizarTranscripcion(`Error: ${event.error}`, 'comando');
    reiniciarReconocimiento();
};

// Añadir botón de activación de voz
function crearBotonVoz() {
    const botonVoz = document.createElement('button');
    botonVoz.textContent = '🎙️ Activar Comandos de Voz';
    botonVoz.classList.add('btn-voz');
    botonVoz.style.position = 'fixed';
    botonVoz.style.bottom = '20px';
    botonVoz.style.right = '20px';
    botonVoz.style.padding = '10px';
    botonVoz.style.backgroundColor = '#000';
    botonVoz.style.color = 'white';
    botonVoz.style.border = 'none';
    botonVoz.style.borderRadius = '5px';
    botonVoz.style.cursor = 'pointer';

    botonVoz.addEventListener('click', () => {
        if (!isListeningForKeyword) {
            iniciarReconocimientoVoz();
            speak('Comandos de voz activados. Di "Mike" para comenzar');
        }
    });

    document.body.appendChild(botonVoz);
}

// Inicializar página con botón de voz y área de transcripción
document.addEventListener('DOMContentLoaded', () => {
    renderizarProductos();
    crearBotonVoz();
    crearAreaTranscripcion();
});
//////////////////////////
////////////////////////////

// Modify the voice command to work with new products
function agregarProductoPorVoz(numeroProducto) {
    const producto = productos[numeroProducto - 1];
    if (producto) {
        agregarAlCarrito(producto.id);
        speak(`Se ha agregado ${producto.nombre} al carrito`);
    } else {
        speak('Producto no encontrado');
    }
}

// Inicializar página
document.addEventListener('DOMContentLoaded', () => {
    renderizarProductos();
    crearBotonVoz();
    crearAreaTranscripcion();
});

document.addEventListener('DOMContentLoaded', mostrarTodosLosProductos);

// Asegúrate de que el botón de filtrar esté correctamente conectado
document.getElementById('btn-filtrar').addEventListener('click', renderizarProductos);
// Inicializar la página
document.addEventListener('DOMContentLoaded', renderizarProductos);