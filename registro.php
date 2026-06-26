<?php
session_start();

$conn = new mysqli("localhost", "root", "", "nanamimus");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$nombre = $_POST['nombre_crear'];
$apellido = $_POST['apellido_crear'];
$fecha = $_POST['fecha_crear'];
$email = $_POST['Email'];
$telefono = $_POST['telefono_crear'];
$clave = $_POST['Contra'];
$confirmar = $_POST['Contra_confi'];

if ($clave !== $confirmar) {
    die("Las contraseñas no coinciden.");
}

$verificar = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$verificar->bind_param("s", $email);
$verificar->execute();
$resultado = $verificar->get_result();

if ($resultado->num_rows > 0) {
    die("Este correo ya está registrado.");
}

// Encriptamos la contraseña correctamente
$clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);

// CORRECCIÓN: Usamos '?' para que bind_param funcione de verdad
$stmt = $conn->prepare("
    INSERT INTO usuarios (nombre, apellido, fecha, email, telefono, clave)
    VALUES (?, ?, ?, ?, ?, ?)
");

// Ahora sí pasamos las 6 variables en orden, incluyendo la clave encriptada
$stmt->bind_param(
    "ssssss",
    $nombre,
    $apellido,
    $fecha,
    $email,
    $telefono,
    $clave_encriptada
);

if ($stmt->execute()) {
    $_SESSION['usuario'] = $nombre;
    $_SESSION['email'] = $email;

    header("Location: inicio_sesion.html");
    exit();
} else {
    echo "Error al registrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>