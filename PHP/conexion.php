<?php
$host = "localhost:3307"; // Cambia si usas otro host
$user = "root";      // Usuario de MySQL
$password = "";      // Contraseña de MySQL
$database = "ihc_login"; // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($host, $user, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
echo "Error "
?>
