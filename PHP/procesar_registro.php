<?php
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];
    $confirmar_contraseña = $_POST['confirmar_contraseña'];

    // Validar que las contraseñas coincidan
    if ($contraseña !== $confirmar_contraseña) {
        echo "<script>alert('Las contraseñas no coinciden'); window.location.href='registro.php';</script>";
        exit();
    }

    // Encriptar la contraseña
    $contraseña_encriptada = password_hash($contraseña, PASSWORD_BCRYPT);

    // Insertar en la base de datos
    $sql = "INSERT INTO usuarios (nombre, apellido, correo, contraseña) 
            VALUES ('$nombre', '$apellido', '$correo', '$contraseña_encriptada')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Registro exitoso'); window.location.href='login.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>
