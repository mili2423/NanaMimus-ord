<?php

$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "nanamimus";

$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
?>