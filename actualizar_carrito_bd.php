<?php
include("conexion.php");
session_start();

// Validamos el usuario logueado (si no hay, por defecto usa el 1)
$id_usuario = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 1; 

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $accion = $data['accion'];
    $id_producto = isset($data['producto_id']) ? intval($data['producto_id']) : 0;
    $cantidad = isset($data['cantidad']) ? intval($data['cantidad']) : 0;

    // 1. ACTUALIZAR O AGREGAR
    if ($accion == 'actualizar') {
        $check_query = "SELECT id_detalle FROM detalle_carrito WHERE id_usuario = $id_usuario AND id_producto = $id_producto";
        $result = $conexion->query($check_query);

        if ($result && $result->num_rows > 0) {
            $update_query = "UPDATE detalle_carrito SET cantidad = $cantidad WHERE id_usuario = $id_usuario AND id_producto = $id_producto";
            $conexion->query($update_query);
        } else {
            $insert_query = "INSERT INTO detalle_carrito (id_usuario, id_producto, cantidad) VALUES ($id_usuario, $id_producto, $cantidad)";
            $conexion->query($insert_query);
        }
        echo json_encode(["status" => "success", "message" => "Cantidad actualizada"]);
        exit;
    } 
    // 2. ELIMINAR UN PRODUCTO
    elseif ($accion == 'eliminar') {
        $delete_query = "DELETE FROM detalle_carrito WHERE id_usuario = $id_usuario AND id_producto = $id_producto";
        $conexion->query($delete_query);
        echo json_encode(["status" => "success", "message" => "Producto eliminado"]);
        exit;
    } 
    // 3. VACIAR CARRITO
    elseif ($accion == 'vaciar') {
        $clear_query = "DELETE FROM detalle_carrito WHERE id_usuario = $id_usuario";
        $conexion->query($clear_query);
        echo json_encode(["status" => "success", "message" => "Carrito vaciado"]);
        exit;
    }
    // 4. OBTENER PRODUCTOS AL CARGAR LA PÁGINA
    elseif ($accion == 'obtener') {
        $query_carrito = "SELECT dc.id_producto, dc.cantidad, p.nombre, p.precio, p.imagen1 
                          FROM detalle_carrito dc 
                          JOIN productos p ON dc.id_producto = p.id 
                          WHERE dc.id_usuario = $id_usuario";
        
        $res = $conexion->query($query_carrito);
        $productos = [];
        
        if ($res && $res->num_rows > 0) {
            while ($fila = $res->fetch_assoc()) {
                $productos[] = [
                    'id' => intval($fila['id_producto']),
                    'nombre' => $fila['nombre'],
                    'precio' => floatval($fila['precio']),
                    'imagen' => $fila['imagen1'],
                    'cantidad' => intval($fila['cantidad'])
                ];
            }
        }
        echo json_encode(["status" => "success", "productos" => $productos]);
        exit;
    }
}

echo json_encode(["status" => "error", "message" => "Petición inválida"]);
?>