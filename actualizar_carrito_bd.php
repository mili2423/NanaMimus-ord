<?php
include("conexion.php");
session_start();

// Validamos si hay un usuario logueado, de lo contrario usamos un ID temporal o de invitado (por ejemplo, 1)
$id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 1; 

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $accion = $data['accion'];
    $id_producto = isset($data['producto_id']) ? intval($data['producto_id']) : 0;
    $cantidad = isset($data['cantidad']) ? intval($data['cantidad']) : 0;

    // 1. ACCIÓN: AGREGAR O ACTUALIZAR CANTIDAD
    if ($accion == 'actualizar') {
        // Buscamos si este usuario ya tiene este producto en su lista
        $check_query = "SELECT id_detalle, cantidad FROM detalle_carrito WHERE id_usuario = $id_usuario AND id_producto = $id_producto";
        $result = $conexion->query($check_query);

        if ($result && $result->num_rows > 0) {
            // Si ya existe, actualizamos la cantidad exacta que viene del JS
            $row = $result->fetch_assoc();
            $update_query = "UPDATE detalle_carrito SET cantidad = $cantidad WHERE id_usuario = $id_usuario AND id_producto = $id_producto";
            $conexion->query($update_query);
        } else {
            // Si es nuevo, lo insertamos desde cero
            $insert_query = "INSERT INTO detalle_carrito (id_usuario, id_producto, cantidad) VALUES ($id_usuario, $id_producto, $cantidad)";
            $conexion->query($insert_query);
        }
    } 
    // 2. ACCIÓN: ELIMINAR UN PRODUCTO ESPECÍFICO
    elseif ($accion == 'eliminar') {
        $delete_query = "DELETE FROM detalle_carrito WHERE id_usuario = $id_usuario AND id_producto = $id_producto";
        $conexion->query($delete_query);
    } 
    // 3. ACCIÓN: VACIAR TODO EL CARRITO
    elseif ($accion == 'vaciar') {
        $clear_query = "DELETE FROM detalle_carrito WHERE id_usuario = $id_usuario";
        $conexion->query($clear_query);
    }

    echo json_encode(["status" => "success", "message" => "Base de datos sincronizada"]);
    exit;
}

echo json_encode(["status" => "error", "message" => "Petición inválida"]);
?>