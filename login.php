<?php
session_start();

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "nanamimus");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Validar que los datos del formulario hayan sido enviados
if (isset($_POST['Email']) && isset($_POST['Contra'])) {
    
    // Capturar los datos del formulario (coincidiendo con los 'name' del HTML)
    $email = $_POST['Email'];
    $clave = $_POST['Contra'];

    // 1. Buscar al usuario por su correo electrónico usando una consulta preparada
    $stmt = $conn->prepare("SELECT id, nombre, clave FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    // 2. Verificar si encontramos una coincidencia para ese correo
    if ($resultado->num_rows === 1) {
        // Obtenemos los datos del usuario de la base de datos
        $usuario = $resultado->fetch_assoc();

        // 3. Verificar si la contraseña ingresada coincide con el hash encriptado
        if (password_verify($clave, $usuario['clave'])) {
            
            // ¡Contraseña correcta! Iniciamos la sesión del usuario
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario'] = $usuario['nombre'];
            $_SESSION['email'] = $email;

            // Redireccionar a la página principal de la tienda
            header("Location: indexNanaMimus.php");
            exit();
        } else {
            // Contraseña incorrecta
            echo "<script>alert('Contraseña incorrecta.'); window.location.href='inicio_sesion.html';</script>";
        }
    } else {
        // No existe ningún usuario con ese correo
        echo "<script>alert('El correo electrónico no está registrado.'); window.location.href='inicio_sesion.html';</script>";
    }

    $stmt->close();
} else {
    // Si intentan entrar a este archivo sin enviar el formulario
    header("Location: inicio_sesion.html");
    exit();
}

$conn->close();
?>