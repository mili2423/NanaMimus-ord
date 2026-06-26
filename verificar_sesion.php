<?php
session_start();
header('Content-Type: application/json');

if (isset($_SESSION['usuario'])) {
    // Si hay una sesión activa, enviamos los datos del usuario
    echo json_encode([
        "autenticado" => true,
        "nombre" => $_SESSION['usuario']
    ]);
} else {
    // Si no ha iniciado sesión
    echo json_encode([
        "autenticado" => false
    ]);
}
exit();
?>