<?php
session_start();

// VALIDACIÓN DE SEGURIDAD GENERAL: Si no hay sesión, rebota la petición
if (!isset($_SESSION['email'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        header('Content-Type: application/json');
        echo json_encode(["error" => "No autorizado"]);
    } else {
        header("Location: inicio_sesion.html");
    }
    exit();
}

// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "nanamimus");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$email_sesion = $_SESSION['email'];

// --- ACCIÓN 1: ENVIAR DATOS AL HTML ---
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['cargar_datos'])) {
    header('Content-Type: application/json');
    
    $stmt = $conn->prepare("SELECT nombre, apellido, fecha, telefono FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email_sesion);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $usuario = $resultado->fetch_assoc();

    $usuario['email'] = $email_sesion;

    echo json_encode($usuario);
    $stmt->close();
    $conn->close();
    exit();
}

// --- ACCIÓN 2: ACTUALIZAR DATOS EN LA BD ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = $_POST['Nombre'];
    $apellido = $_POST['Apellido'];
    $fecha    = $_POST['FechaNac'];
    $telefono = $_POST['Telefono'];
    $clave    = $_POST['Contra'];

    if (!empty($clave)) {
        $clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);
        $stmt_update = $conn->prepare("UPDATE usuarios SET nombre=?, apellido=?, fecha=?, telefono=?, clave=? WHERE email=?");
        $stmt_update->bind_param("ssssss", $nombre, $apellido, $fecha, $telefono, $clave_encriptada, $email_sesion);
    } else {
        $stmt_update = $conn->prepare("UPDATE usuarios SET nombre=?, apellido=?, fecha=?, telefono=? WHERE email=?");
        $stmt_update->bind_param("sssss", $nombre, $apellido, $fecha, $telefono, $email_sesion);
    }

    if ($stmt_update->execute()) {
        $_SESSION['usuario'] = $nombre; 
        echo "<script>alert('¡Datos actualizados correctamente!'); window.location.href='perfil.html';</script>";
    } else {
        echo "<script>alert('Error al actualizar: " . $stmt_update->error . "'); window.location.href='perfil.html';</script>";
    }
    
    $stmt_update->close();
    $conn->close();
    exit();
}
?>