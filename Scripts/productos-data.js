// ============================================================
// DATOS CENTRALIZADOS DE PRODUCTOS — MikeZapatillas
// ============================================================

const PRODUCTOS = [
    // ==================== HOMBRES ====================
    {
        id: 1,
        nombre: 'Zapatillas Adidas Terrex Trailmaker',
        precio: 259.90,
        precioOriginal: 319.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/z/gz5694_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/z/gz5694_1.jpg',
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/z/gz5694_1.jpg'
        ],
        categoria: 'Outdoor',
        genero: 'hombre',
        marca: 'Adidas',
        descripcion: 'Zapatillas de trail running diseñadas para terrenos irregulares. Cuentan con suela Traxion para un agarre excepcional y una entresuela de EVA para amortiguación ligera. Parte superior de malla transpirable con refuerzos sintéticos.',
        tallas: [38, 39, 40, 41, 42, 43, 44],
        colores: ['Negro/Gris', 'Verde/Negro'],
        material: 'Malla transpirable con refuerzos sintéticos',
        nuevo: false,
        enOferta: true,
        stock: { 38: 3, 39: 5, 40: 8, 41: 10, 42: 7, 43: 4, 44: 2 },
        resenas: [
            { usuario: 'Carlos M.', puntuacion: 5, comentario: 'Excelentes para senderismo, muy cómodas.', fecha: '2024-11-15' },
            { usuario: 'Pedro L.', puntuacion: 4, comentario: 'Buen agarre en terreno húmedo.', fecha: '2024-10-22' }
        ]
    },
    {
        id: 2,
        nombre: 'Zapatillas Nike Waffle Debut',
        precio: 253.91,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/d/v/dv0743-100_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/d/v/dv0743-100_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'hombre',
        marca: 'Nike',
        descripcion: 'Estilo retro inspirado en el diseño clásico de Nike. Suela waffle icónica para tracción duradera. Parte superior de ante y malla para un look vintage con comodidad moderna.',
        tallas: [39, 40, 41, 42, 43, 44],
        colores: ['Blanco', 'Blanco/Negro'],
        material: 'Ante y malla',
        nuevo: false,
        enOferta: false,
        stock: { 39: 6, 40: 10, 41: 12, 42: 8, 43: 5, 44: 3 },
        resenas: [
            { usuario: 'Miguel R.', puntuacion: 5, comentario: 'El diseño retro es espectacular.', fecha: '2024-12-01' },
            { usuario: 'Andrés S.', puntuacion: 4, comentario: 'Muy cómodas para el día a día.', fecha: '2024-11-28' }
        ]
    },
    {
        id: 3,
        nombre: 'Zapatillas Nike Flex Experience RN 11',
        precio: 189.00,
        precioOriginal: 229.00,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/d/d/dd9284-001_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/d/d/dd9284-001_1.jpg'
        ],
        categoria: 'Correr',
        genero: 'hombre',
        marca: 'Nike',
        descripcion: 'Zapatillas de running ligeras con suela flexible que se adapta al movimiento natural del pie. Amortiguación suave para carreras cortas y entrenamientos diarios.',
        tallas: [39, 40, 41, 42, 43, 44, 45],
        colores: ['Negro', 'Gris/Azul'],
        material: 'Malla de ingeniería',
        nuevo: false,
        enOferta: true,
        stock: { 39: 4, 40: 7, 41: 10, 42: 9, 43: 6, 44: 3, 45: 2 },
        resenas: [
            { usuario: 'Juan P.', puntuacion: 4, comentario: 'Muy ligeras, ideales para correr.', fecha: '2024-11-10' },
            { usuario: 'Diego F.', puntuacion: 5, comentario: 'La mejor relación calidad-precio.', fecha: '2024-10-05' },
            { usuario: 'Raúl T.', puntuacion: 3, comentario: 'Buenas pero la suela se desgasta rápido.', fecha: '2024-09-20' }
        ]
    },
    {
        id: 4,
        nombre: 'Zapatillas Nike Waffle Debut Premium',
        precio: 289.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/d/h/dh9522-001_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/d/h/dh9522-001_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'hombre',
        marca: 'Nike',
        descripcion: 'Versión premium de la icónica Waffle Debut. Materiales de primera calidad con acabados superiores para un look más refinado.',
        tallas: [39, 40, 41, 42, 43, 44],
        colores: ['Negro'],
        material: 'Cuero y ante premium',
        nuevo: true,
        enOferta: false,
        stock: { 39: 3, 40: 5, 41: 8, 42: 6, 43: 4, 44: 2 },
        resenas: [
            { usuario: 'Fernando G.', puntuacion: 5, comentario: 'Calidad premium, se nota la diferencia.', fecha: '2024-12-10' }
        ]
    },
    {
        id: 5,
        nombre: 'Zapatillas Adidas EQ21 Run',
        precio: 209.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/0/h00521_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/0/h00521_1.jpg'
        ],
        categoria: 'Correr',
        genero: 'hombre',
        marca: 'Adidas',
        descripcion: 'Zapatillas de running con tecnología Bounce para una amortiguación con retorno de energía. Diseñadas para corredores que buscan comodidad en cada kilómetro.',
        tallas: [39, 40, 41, 42, 43, 44],
        colores: ['Negro/Blanco'],
        material: 'Malla transpirable',
        nuevo: false,
        enOferta: false,
        stock: { 39: 5, 40: 8, 41: 10, 42: 7, 43: 4, 44: 3 },
        resenas: [
            { usuario: 'Roberto M.', puntuacion: 4, comentario: 'Cómodas para correr distancias medias.', fecha: '2024-11-05' },
            { usuario: 'Sergio V.', puntuacion: 4, comentario: 'Buena amortiguación, las recomiendo.', fecha: '2024-10-15' }
        ]
    },
    {
        id: 6,
        nombre: 'Zapatillas Adidas Solematch Control',
        precio: 279.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/y/gy4691_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/y/gy4691_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'hombre',
        marca: 'Adidas',
        descripcion: 'Zapatillas versátiles con diseño moderno. Suela de goma resistente y parte superior de materiales mixtos para un estilo urbano sofisticado.',
        tallas: [39, 40, 41, 42, 43],
        colores: ['Blanco/Negro'],
        material: 'Sintético y malla',
        nuevo: false,
        enOferta: false,
        stock: { 39: 4, 40: 6, 41: 8, 42: 5, 43: 3 },
        resenas: [
            { usuario: 'Luis A.', puntuacion: 5, comentario: 'Estilo urbano muy elegante.', fecha: '2024-11-20' }
        ]
    },
    {
        id: 7,
        nombre: 'Zapatillas Puma Flyer Runner Mesh',
        precio: 139.00,
        precioOriginal: 179.00,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/1/9/195343-13_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/1/9/195343-13_1.jpg'
        ],
        categoria: 'Correr',
        genero: 'hombre',
        marca: 'Puma',
        descripcion: 'Zapatillas de running accesibles con malla transpirable y suela de goma para un buen agarre. Perfectas para corredores principiantes.',
        tallas: [39, 40, 41, 42, 43, 44],
        colores: ['Negro/Blanco'],
        material: 'Malla',
        nuevo: false,
        enOferta: true,
        stock: { 39: 10, 40: 12, 41: 15, 42: 10, 43: 7, 44: 4 },
        resenas: [
            { usuario: 'Marco T.', puntuacion: 4, comentario: 'Buena relación calidad-precio.', fecha: '2024-10-30' },
            { usuario: 'Alex R.', puntuacion: 3, comentario: 'Cómodas pero bastante básicas.', fecha: '2024-09-15' }
        ]
    },
    {
        id: 8,
        nombre: 'Zapatillas Puma Flyer Runner Mesh II',
        precio: 149.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/1/9/195343-14_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/1/9/195343-14_1.jpg'
        ],
        categoria: 'Correr',
        genero: 'hombre',
        marca: 'Puma',
        descripcion: 'Segunda generación del popular Flyer Runner. Mejoras en amortiguación y transpirabilidad para un rendimiento superior.',
        tallas: [39, 40, 41, 42, 43, 44],
        colores: ['Azul/Blanco'],
        material: 'Malla reforzada',
        nuevo: false,
        enOferta: false,
        stock: { 39: 5, 40: 8, 41: 10, 42: 7, 43: 4, 44: 3 },
        resenas: []
    },
    {
        id: 9,
        nombre: 'Zapatillas Michelin Desert Race DR05',
        precio: 315.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/0/201h-dr05-01-01_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/0/201h-dr05-01-01_1.jpg'
        ],
        categoria: 'Outdoor',
        genero: 'hombre',
        marca: 'Michelin',
        descripcion: 'Zapatillas todoterreno con suela Michelin para un agarre extremo. Diseñadas para aventuras al aire libre en cualquier superficie.',
        tallas: [40, 41, 42, 43, 44],
        colores: ['Marrón/Negro'],
        material: 'Cuero sintético resistente al agua',
        nuevo: false,
        enOferta: false,
        stock: { 40: 4, 41: 6, 42: 5, 43: 3, 44: 2 },
        resenas: [
            { usuario: 'Javier C.', puntuacion: 5, comentario: 'El agarre Michelin es increíble.', fecha: '2024-11-25' },
            { usuario: 'David P.', puntuacion: 5, comentario: 'Perfectas para trekking.', fecha: '2024-10-18' }
        ]
    },
    {
        id: 10,
        nombre: 'Zapatillas Michelin Desert Race DR05 II',
        precio: 315.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1h-dr05-01-09_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1h-dr05-01-09_1.jpg'
        ],
        categoria: 'Outdoor',
        genero: 'hombre',
        marca: 'Michelin',
        descripcion: 'Segunda versión de la Desert Race DR05 con mejoras en la transpirabilidad y un diseño más ligero sin perder la robustez.',
        tallas: [40, 41, 42, 43, 44],
        colores: ['Gris/Verde'],
        material: 'Textil y sintético reforzado',
        nuevo: true,
        enOferta: false,
        stock: { 40: 3, 41: 5, 42: 4, 43: 3, 44: 2 },
        resenas: []
    },
    {
        id: 11,
        nombre: 'Zapatillas Michelin Desert Race DR09',
        precio: 299.90,
        precioOriginal: 349.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1h-dr09-31-01_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1h-dr09-31-01_1.jpg'
        ],
        categoria: 'Outdoor',
        genero: 'hombre',
        marca: 'Michelin',
        descripcion: 'Modelo avanzado de la línea Desert Race. Suela Michelin de última generación con máximo agarre y durabilidad.',
        tallas: [39, 40, 41, 42, 43, 44],
        colores: ['Negro/Rojo'],
        material: 'Nylon balístico',
        nuevo: false,
        enOferta: true,
        stock: { 39: 2, 40: 4, 41: 6, 42: 5, 43: 3, 44: 2 },
        resenas: [
            { usuario: 'Tomás H.', puntuacion: 4, comentario: 'Muy resistentes, perfectas para montaña.', fecha: '2024-12-05' }
        ]
    },
    {
        id: 12,
        nombre: 'Zapatillas Michelin Pilot Sport',
        precio: 139.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1-ps15-01-17_1_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1-ps15-01-17_1_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'hombre',
        marca: 'Michelin',
        descripcion: 'Estilo deportivo inspirado en el automovilismo. Suela Michelin Pilot Sport para agarre en superficies urbanas.',
        tallas: [39, 40, 41, 42, 43],
        colores: ['Negro/Rojo'],
        material: 'Malla con overlay de PU',
        nuevo: false,
        enOferta: false,
        stock: { 39: 6, 40: 8, 41: 10, 42: 7, 43: 5 },
        resenas: [
            { usuario: 'Enrique M.', puntuacion: 4, comentario: 'Diseño llamativo y muy cómoda.', fecha: '2024-11-12' }
        ]
    },
    // ==================== MUJERES ====================
    {
        id: 100,
        nombre: 'Zapatillas Michelin Pilot Sport W',
        precio: 139.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1-ps15-03-08_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1-ps15-03-08_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'mujeres',
        marca: 'Michelin',
        descripcion: 'Versión femenina de la Pilot Sport con diseño más estilizado y ajuste adaptado al pie de la mujer.',
        tallas: [35, 36, 37, 38, 39, 40],
        colores: ['Rosa/Blanco'],
        material: 'Malla ligera',
        nuevo: false,
        enOferta: false,
        stock: { 35: 4, 36: 7, 37: 10, 38: 8, 39: 5, 40: 3 },
        resenas: [
            { usuario: 'María L.', puntuacion: 5, comentario: 'Muy bonitas y cómodas.', fecha: '2024-12-01' }
        ]
    },
    {
        id: 101,
        nombre: 'Zapatillas Michelin Pilot Sport Rosa',
        precio: 139.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1-ps15-08-03_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1-ps15-08-03_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'mujeres',
        marca: 'Michelin',
        descripcion: 'Versión en color rosa de la popular Pilot Sport. Estilo femenino y deportivo con la confianza de la suela Michelin.',
        tallas: [35, 36, 37, 38, 39, 40],
        colores: ['Rosa'],
        material: 'Malla con detalles sintéticos',
        nuevo: false,
        enOferta: false,
        stock: { 35: 3, 36: 6, 37: 9, 38: 7, 39: 4, 40: 2 },
        resenas: []
    },
    {
        id: 102,
        nombre: 'Zapatillas Michelin Protek Urban',
        precio: 159.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1-pu03-02-03_1_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1-pu03-02-03_1_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'mujeres',
        marca: 'Michelin',
        descripcion: 'Zapatillas urbanas con protección reforzada y suela Michelin para mayor durabilidad en superficies urbanas.',
        tallas: [35, 36, 37, 38, 39, 40],
        colores: ['Blanco/Rosa'],
        material: 'Textil y sintético',
        nuevo: false,
        enOferta: false,
        stock: { 35: 5, 36: 8, 37: 10, 38: 7, 39: 4, 40: 3 },
        resenas: [
            { usuario: 'Ana G.', puntuacion: 4, comentario: 'Cómodas para caminar todo el día.', fecha: '2024-11-08' }
        ]
    },
    {
        id: 103,
        nombre: 'Zapatillas Michelin Protek Outdoor W',
        precio: 239.00,
        precioOriginal: 289.00,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1-pu06-32-08_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1-pu06-32-08_1.jpg'
        ],
        categoria: 'Outdoor',
        genero: 'mujeres',
        marca: 'Michelin',
        descripcion: 'Zapatillas outdoor con protección reforzada para aventuras al aire libre. Suela Michelin para agarre en terrenos difíciles.',
        tallas: [36, 37, 38, 39, 40],
        colores: ['Verde/Gris'],
        material: 'Nylon resistente al agua',
        nuevo: false,
        enOferta: true,
        stock: { 36: 3, 37: 5, 38: 6, 39: 4, 40: 2 },
        resenas: [
            { usuario: 'Lucía R.', puntuacion: 5, comentario: 'Perfectas para senderismo.', fecha: '2024-10-25' }
        ]
    },
    {
        id: 104,
        nombre: 'Zapatillas Michelin Desert Race DR09 W',
        precio: 299.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1w-dr09-12-28_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q1w-dr09-12-28_1.jpg'
        ],
        categoria: 'Outdoor',
        genero: 'mujeres',
        marca: 'Michelin',
        descripcion: 'Versión femenina de la Desert Race DR09. Máximo agarre y durabilidad con un ajuste adaptado al pie femenino.',
        tallas: [36, 37, 38, 39, 40],
        colores: ['Coral/Negro'],
        material: 'Cuero sintético',
        nuevo: true,
        enOferta: false,
        stock: { 36: 2, 37: 4, 38: 5, 39: 3, 40: 2 },
        resenas: []
    },
    {
        id: 105,
        nombre: 'Zapatillas Michelin LXT',
        precio: 239.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q2w-lx08-02-02_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q2w-lx08-02-02_1.jpg'
        ],
        categoria: 'Outdoor',
        genero: 'mujeres',
        marca: 'Michelin',
        descripcion: 'Zapatillas de aventura con tecnología LXT para máximo rendimiento en terrenos variados. Ligeras y resistentes.',
        tallas: [36, 37, 38, 39, 40],
        colores: ['Azul/Gris'],
        material: 'Textil técnico',
        nuevo: false,
        enOferta: false,
        stock: { 36: 4, 37: 6, 38: 7, 39: 5, 40: 3 },
        resenas: [
            { usuario: 'Carmen D.', puntuacion: 4, comentario: 'Muy ligeras para ser outdoor.', fecha: '2024-11-18' }
        ]
    },
    {
        id: 106,
        nombre: 'Zapatillas Michelin Protek Urban Lite',
        precio: 149.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q2w-pu07-01-26_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q2w-pu07-01-26_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'mujeres',
        marca: 'Michelin',
        descripcion: 'Versión ligera de la Protek Urban. Ideal para el uso diario con estilo y comodidad.',
        tallas: [35, 36, 37, 38, 39, 40],
        colores: ['Blanco'],
        material: 'Malla ultraligera',
        nuevo: false,
        enOferta: false,
        stock: { 35: 6, 36: 8, 37: 10, 38: 8, 39: 5, 40: 3 },
        resenas: []
    },
    {
        id: 107,
        nombre: 'Zapatillas Michelin Protek Urban Classic',
        precio: 149.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q2w-pu07-10-09_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/2/2/22q2w-pu07-10-09_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'mujeres',
        marca: 'Michelin',
        descripcion: 'Diseño clásico urbano con la confianza de Michelin. Perfectas para combinar con cualquier outfit.',
        tallas: [35, 36, 37, 38, 39, 40],
        colores: ['Gris/Rosa'],
        material: 'Ante sintético',
        nuevo: false,
        enOferta: false,
        stock: { 35: 4, 36: 7, 37: 9, 38: 7, 39: 4, 40: 2 },
        resenas: [
            { usuario: 'Sofía M.', puntuacion: 5, comentario: 'Combinan con todo, las amo.', fecha: '2024-12-08' }
        ]
    },
    {
        id: 108,
        nombre: 'Zapatillas Under Armour UA Hovr Sonic 3',
        precio: 249.90,
        precioOriginal: 299.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/3/0/3022596-603_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/3/0/3022596-603_1.jpg'
        ],
        categoria: 'Correr',
        genero: 'mujeres',
        marca: 'Under Armour',
        descripcion: 'Zapatillas de running con tecnología HOVR para cero gravedad. Conectividad con la app UA MapMyRun para tracking de rendimiento.',
        tallas: [36, 37, 38, 39, 40],
        colores: ['Rosa/Blanco'],
        material: 'Malla de ingeniería ligera',
        nuevo: false,
        enOferta: true,
        stock: { 36: 3, 37: 5, 38: 6, 39: 4, 40: 2 },
        resenas: [
            { usuario: 'Valentina P.', puntuacion: 5, comentario: 'La tecnología HOVR es increíble.', fecha: '2024-11-22' },
            { usuario: 'Paula S.', puntuacion: 4, comentario: 'Muy cómodas para correr largas distancias.', fecha: '2024-10-30' }
        ]
    },
    {
        id: 109,
        nombre: 'Zapatillas Under Armour Hovr Sonic 4',
        precio: 189.90,
        precioOriginal: 239.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/3/0/3023559-603_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/3/0/3023559-603_1.jpg'
        ],
        categoria: 'Correr',
        genero: 'mujeres',
        marca: 'Under Armour',
        descripcion: 'Cuarta generación de la popular línea HOVR Sonic. Mejoras en amortiguación y respuesta para un running más eficiente.',
        tallas: [36, 37, 38, 39, 40],
        colores: ['Rosa/Negro'],
        material: 'Malla Intelliknit',
        nuevo: false,
        enOferta: true,
        stock: { 36: 5, 37: 7, 38: 8, 39: 5, 40: 3 },
        resenas: [
            { usuario: 'Isabella F.', puntuacion: 4, comentario: 'Mejores que la versión anterior.', fecha: '2024-11-15' }
        ]
    },
    {
        id: 110,
        nombre: 'Zapatillas Under Armour Charged Pursuit 3',
        precio: 159.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/3/0/3024889-001_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/3/0/3024889-001_1.jpg'
        ],
        categoria: 'Correr',
        genero: 'mujeres',
        marca: 'Under Armour',
        descripcion: 'Amortiguación Charged Cushioning para una pisada suave. Malla transpirable y suela de goma resistente.',
        tallas: [36, 37, 38, 39, 40],
        colores: ['Negro'],
        material: 'Malla transpirable',
        nuevo: false,
        enOferta: false,
        stock: { 36: 6, 37: 9, 38: 10, 39: 7, 40: 4 },
        resenas: [
            { usuario: 'Diana C.', puntuacion: 4, comentario: 'Buena opción económica.', fecha: '2024-10-20' },
            { usuario: 'Laura T.', puntuacion: 3, comentario: 'Cómodas pero nada especial.', fecha: '2024-09-28' }
        ]
    },
    {
        id: 111,
        nombre: 'Zapatillas Under Armour Charged Breathe',
        precio: 169.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/3/0/3025058-600_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/3/0/3025058-600_1.jpg'
        ],
        categoria: 'Entrenar',
        genero: 'mujeres',
        marca: 'Under Armour',
        descripcion: 'Diseñadas para entrenamiento en el gym. Suela plana para estabilidad en levantamiento de pesas y amortiguación para ejercicios cardio.',
        tallas: [36, 37, 38, 39, 40],
        colores: ['Rosa/Blanco'],
        material: 'Malla con soporte lateral',
        nuevo: false,
        enOferta: false,
        stock: { 36: 4, 37: 6, 38: 7, 39: 5, 40: 3 },
        resenas: [
            { usuario: 'Camila V.', puntuacion: 5, comentario: 'Perfectas para el gym.', fecha: '2024-12-02' }
        ]
    },
    // ==================== NIÑOS ====================
    {
        id: 200,
        nombre: 'Zapatillas Puma Amare',
        precio: 139.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/3/7/376209-07_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/3/7/376209-07_1.jpg'
        ],
        categoria: 'Correr',
        genero: 'niños',
        marca: 'Puma',
        descripcion: 'Zapatillas de running para niños con cierre de velcro para fácil calce. Suela de goma antideslizante para máxima seguridad.',
        tallas: [28, 29, 30, 31, 32, 33, 34],
        colores: ['Negro/Verde'],
        material: 'Malla y sintético',
        nuevo: false,
        enOferta: false,
        stock: { 28: 5, 29: 7, 30: 10, 31: 8, 32: 6, 33: 4, 34: 3 },
        resenas: [
            { usuario: 'Patricia M.', puntuacion: 5, comentario: 'A mi hijo le encantan, muy fácil de poner.', fecha: '2024-11-30' }
        ]
    },
    {
        id: 201,
        nombre: 'Zapatillas Nike Court Borough Low 2',
        precio: 179.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/b/q/bq5451-104_1_1_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/b/q/bq5451-104_1_1_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'niños',
        marca: 'Nike',
        descripcion: 'Zapatillas urbanas para niños con diseño inspirado en el basketball. Cierre de cordones y puntera reforzada.',
        tallas: [28, 29, 30, 31, 32, 33, 34, 35],
        colores: ['Blanco'],
        material: 'Cuero sintético',
        nuevo: false,
        enOferta: false,
        stock: { 28: 4, 29: 6, 30: 8, 31: 10, 32: 7, 33: 5, 34: 3, 35: 2 },
        resenas: [
            { usuario: 'Gloria P.', puntuacion: 4, comentario: 'Bonitas y resistentes para niños.', fecha: '2024-11-05' }
        ]
    },
    {
        id: 202,
        nombre: 'Zapatillas Adidas Grand Court 2.0 EL',
        precio: 169.90,
        precioOriginal: 199.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/w/gw6521_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/w/gw6521_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'niños',
        marca: 'Adidas',
        descripcion: 'Zapatillas clásicas Adidas para niños con cierre de velcro. Diseño atemporal con las 3 rayas icónicas.',
        tallas: [28, 29, 30, 31, 32, 33, 34],
        colores: ['Blanco/Verde'],
        material: 'Cuero sintético',
        nuevo: false,
        enOferta: true,
        stock: { 28: 6, 29: 8, 30: 10, 31: 9, 32: 7, 33: 4, 34: 3 },
        resenas: [
            { usuario: 'Elena R.', puntuacion: 5, comentario: 'Clásicas Adidas, nunca fallan.', fecha: '2024-11-20' },
            { usuario: 'Ricardo S.', puntuacion: 4, comentario: 'Mi hijo las usa todos los días.', fecha: '2024-10-15' }
        ]
    },
    {
        id: 203,
        nombre: 'Zapatillas Adidas Predator Accuracy TF J',
        precio: 179.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/w/gw7083_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/w/gw7083_1.jpg'
        ],
        categoria: 'Fútbol',
        genero: 'niños',
        marca: 'Adidas',
        descripcion: 'Zapatillas de fútbol para césped artificial. Diseño Predator para mayor control del balón.',
        tallas: [30, 31, 32, 33, 34, 35, 36],
        colores: ['Negro/Rosa'],
        material: 'Sintético con textura Control Skin',
        nuevo: false,
        enOferta: false,
        stock: { 30: 4, 31: 6, 32: 8, 33: 7, 34: 5, 35: 3, 36: 2 },
        resenas: [
            { usuario: 'Manuel T.', puntuacion: 5, comentario: 'Mi hijo juega mucho mejor con estas.', fecha: '2024-11-28' }
        ]
    },
    {
        id: 204,
        nombre: 'Zapatillas Adidas Racer TR21 Woody',
        precio: 129.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/y/gy4450_1_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/y/gy4450_1_1.jpg'
        ],
        categoria: 'Correr',
        genero: 'niños',
        marca: 'Adidas',
        descripcion: 'Zapatillas temáticas de Toy Story con Woody. Diseño divertido y colorido para los más pequeños.',
        tallas: [22, 23, 24, 25, 26, 27],
        colores: ['Amarillo/Marrón'],
        material: 'Malla y sintético',
        nuevo: false,
        enOferta: false,
        stock: { 22: 5, 23: 7, 24: 8, 25: 6, 26: 4, 27: 3 },
        resenas: [
            { usuario: 'Sandra L.', puntuacion: 5, comentario: 'A mi hijo le fascinan, son de Woody!', fecha: '2024-12-10' }
        ]
    },
    {
        id: 205,
        nombre: 'Zapatillas Adidas Vulc Raid3R Muppets',
        precio: 139.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/z/gz1700_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/z/gz1700_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'niños',
        marca: 'Adidas',
        descripcion: 'Colaboración especial con los Muppets. Diseño vulcanizado clásico con detalles temáticos divertidos.',
        tallas: [28, 29, 30, 31, 32, 33, 34],
        colores: ['Blanco/Verde'],
        material: 'Canvas y sintético',
        nuevo: false,
        enOferta: false,
        stock: { 28: 3, 29: 5, 30: 7, 31: 6, 32: 4, 33: 3, 34: 2 },
        resenas: []
    },
    {
        id: 206,
        nombre: 'Zapatillas Adidas Advantage K',
        precio: 89.90,
        precioOriginal: 119.90,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/0/h06181_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/0/h06181_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'niños',
        marca: 'Adidas',
        descripcion: 'Zapatillas escolares clásicas con diseño limpio y sencillo. Ideales para el uso diario.',
        tallas: [31, 32, 33, 34, 35, 36],
        colores: ['Blanco'],
        material: 'Cuero sintético',
        nuevo: false,
        enOferta: true,
        stock: { 31: 8, 32: 10, 33: 12, 34: 9, 35: 6, 36: 4 },
        resenas: [
            { usuario: 'Jessica M.', puntuacion: 4, comentario: 'Excelente precio para el colegio.', fecha: '2024-10-08' },
            { usuario: 'Carolina A.', puntuacion: 5, comentario: 'Las mejores para el día a día.', fecha: '2024-09-15' }
        ]
    },
    {
        id: 207,
        nombre: 'Zapatillas Adidas VulcRaid3R Spiderman',
        precio: 179.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/p/hp7750_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/p/hp7750_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'niños',
        marca: 'Adidas',
        descripcion: 'Zapatillas temáticas de Spider-Man. Diseño con colores y detalles del superhéroe favorito de los niños.',
        tallas: [28, 29, 30, 31, 32, 33, 34],
        colores: ['Rojo/Azul'],
        material: 'Sintético y canvas',
        nuevo: true,
        enOferta: false,
        stock: { 28: 4, 29: 6, 30: 8, 31: 7, 32: 5, 33: 3, 34: 2 },
        resenas: [
            { usuario: 'Marta G.', puntuacion: 5, comentario: 'Mi hijo no se las quiere quitar!', fecha: '2024-12-15' }
        ]
    },
    {
        id: 208,
        nombre: 'Zapatillas Adidas Breaknet 2.0 K',
        precio: 113.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/p/hp8956_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/p/hp8956_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'niños',
        marca: 'Adidas',
        descripcion: 'Estilo clásico tenis con diseño retro. Cómodas y versátiles para el uso diario.',
        tallas: [31, 32, 33, 34, 35, 36],
        colores: ['Blanco/Azul'],
        material: 'Cuero sintético',
        nuevo: false,
        enOferta: false,
        stock: { 31: 6, 32: 8, 33: 10, 34: 7, 35: 5, 36: 3 },
        resenas: []
    },
    {
        id: 209,
        nombre: 'Zapatillas Adidas Cross EM Up 5 K',
        precio: 186.00,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/q/hq8494_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/h/q/hq8494_1.jpg'
        ],
        categoria: 'Básquet',
        genero: 'niños',
        marca: 'Adidas',
        descripcion: 'Zapatillas de basketball para jóvenes jugadores. Soporte en el tobillo y suela de tracción para la cancha.',
        tallas: [32, 33, 34, 35, 36, 37],
        colores: ['Negro/Rojo'],
        material: 'Sintético con soporte textil',
        nuevo: false,
        enOferta: false,
        stock: { 32: 4, 33: 6, 34: 7, 35: 5, 36: 3, 37: 2 },
        resenas: [
            { usuario: 'Roberto F.', puntuacion: 5, comentario: 'Excelente soporte para basketball.', fecha: '2024-11-10' }
        ]
    },
    {
        id: 210,
        nombre: 'Zapatillas Adidas Advantage K Premium',
        precio: 149.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/y/gy6995_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/g/y/gy6995_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'niños',
        marca: 'Adidas',
        descripcion: 'Versión premium de la Advantage K con materiales de mejor calidad y diseño más refinado.',
        tallas: [31, 32, 33, 34, 35, 36],
        colores: ['Blanco/Rosa'],
        material: 'Cuero sintético premium',
        nuevo: false,
        enOferta: false,
        stock: { 31: 5, 32: 7, 33: 9, 34: 6, 35: 4, 36: 3 },
        resenas: []
    },
    {
        id: 211,
        nombre: 'Zapatillas Adidas Grand Court Spider',
        precio: 159.90,
        precioOriginal: null,
        imagen: 'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/i/f/if9893_1.jpg',
        imagenes: [
            'https://runastore.pe/media/catalog/product/cache/22449fb4008bd7c4d5ef9f24f3c232e5/i/f/if9893_1.jpg'
        ],
        categoria: 'Urbano',
        genero: 'niños',
        marca: 'Adidas',
        descripcion: 'Grand Court con temática de Spider-Man. Combina el estilo clásico Adidas con el superhéroe favorito.',
        tallas: [28, 29, 30, 31, 32, 33, 34],
        colores: ['Rojo/Negro'],
        material: 'Cuero sintético',
        nuevo: true,
        enOferta: false,
        stock: { 28: 3, 29: 5, 30: 7, 31: 6, 32: 4, 33: 3, 34: 2 },
        resenas: [
            { usuario: 'Fernanda K.', puntuacion: 5, comentario: 'Diseño hermoso, a mi hijo le fascinan.', fecha: '2024-12-12' }
        ]
    }
];

// Guía de tallas
const GUIA_TALLAS = {
    hombre: [
        { eu: 39, us: 6.5, uk: 6, cm: 24.5 },
        { eu: 40, us: 7, uk: 6.5, cm: 25 },
        { eu: 41, us: 8, uk: 7.5, cm: 26 },
        { eu: 42, us: 8.5, uk: 8, cm: 26.5 },
        { eu: 43, us: 9.5, uk: 9, cm: 27.5 },
        { eu: 44, us: 10, uk: 9.5, cm: 28 },
        { eu: 45, us: 11, uk: 10.5, cm: 29 }
    ],
    mujeres: [
        { eu: 35, us: 5, uk: 2.5, cm: 22 },
        { eu: 36, us: 5.5, uk: 3.5, cm: 22.5 },
        { eu: 37, us: 6.5, uk: 4, cm: 23.5 },
        { eu: 38, us: 7, uk: 5, cm: 24 },
        { eu: 39, us: 8, uk: 5.5, cm: 24.5 },
        { eu: 40, us: 8.5, uk: 6.5, cm: 25.5 }
    ],
    'niños': [
        { eu: 22, us: 6, uk: 5.5, cm: 13.5 },
        { eu: 23, us: 7, uk: 6, cm: 14 },
        { eu: 24, us: 7.5, uk: 7, cm: 14.5 },
        { eu: 25, us: 8, uk: 7.5, cm: 15 },
        { eu: 26, us: 9, uk: 8, cm: 15.5 },
        { eu: 27, us: 9.5, uk: 9, cm: 16.5 },
        { eu: 28, us: 10.5, uk: 10, cm: 17 },
        { eu: 29, us: 11.5, uk: 11, cm: 18 },
        { eu: 30, us: 12, uk: 11.5, cm: 18.5 },
        { eu: 31, us: 13, uk: 12.5, cm: 19 },
        { eu: 32, us: 1, uk: 13.5, cm: 20 },
        { eu: 33, us: 1.5, uk: 1, cm: 20.5 },
        { eu: 34, us: 2.5, uk: 2, cm: 21 },
        { eu: 35, us: 3, uk: 2.5, cm: 21.5 },
        { eu: 36, us: 4, uk: 3.5, cm: 22.5 },
        { eu: 37, us: 5, uk: 4, cm: 23.5 }
    ]
};
