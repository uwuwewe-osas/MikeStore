<?php
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $contraseña = $_POST['contraseña'];

    // Consulta para obtener el usuario
    $sql = "SELECT * FROM usuarios WHERE correo = '$correo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($contraseña, $user['contraseña'])) {
            $_SESSION['correo'] = $correo;
            header("Location: ../Html/index.html");
            exit();
        } else {
            echo "<script>alert('Contraseña incorrecta'); window.location.href='login.php';</script>";
        }
    } else {
        echo "<script>alert('Usuario no encontrado'); window.location.href='login.php';</script>";
    }

    $conn->close();
}
?>
