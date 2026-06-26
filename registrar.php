<?php
session_start();
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "nanamimus");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// CORRECCIÓN: Ajuste de nombres según los 'name' del HTML
$nombre    = $_POST['Nombre'];
$apellido  = $_POST['Apellido'];
$fecha     = $_POST['FechaNac'];
$email     = $_POST['Email'];
$telefono  = $_POST['Telefono'];
$clave     = $_POST['Contra'];
$confirmar = $_POST['ConfirmarContra'];

// Validar que las contraseñas coincidan
if ($clave !== $confirmar) {
    die("Las contraseñas no coinciden.");
}

// Verificar si el correo ya existe
$verificar = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
$verificar->bind_param("s", $email);
$verificar->execute();
$resultado = $verificar->get_result();

if ($resultado->num_rows > 0) {
    die("Este correo ya está registrado.");
}
$verificar->close();

// Encriptar la contraseña
$clave_encriptada = password_hash($clave, PASSWORD_DEFAULT);

// Uso correcto de marcadores '?' para la consulta preparada
$stmt = $conn->prepare("
    INSERT INTO usuarios (nombre, apellido, fecha, email, telefono, clave)
    VALUES (?, ?, ?, ?, ?, ?)
");

// Vincular los parámetros reales (incluyendo la clave encriptada)
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

    // Redireccionar al inicio de sesión tras el éxito
    header("Location: inicio_sesion.html");
    exit();
} else {
    echo "Error al registrar: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>