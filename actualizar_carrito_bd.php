<?php
include("conexion.php");
session_start();

$pedido_id = isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 1; 
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $accion = $data['accion'];
    $producto_id = isset($data['producto_id']) ? intval($data['producto_id']) : 0;
    $cantidad = isset($data['cantidad']) ? intval($data['cantidad']) : 0;

    $precio = 0;
    if ($producto_id > 0) {
        $res_precio = $conexion->query("SELECT precio FROM productos WHERE id = $producto_id");
        if ($res_precio && $res_precio->num_rows > 0) {
            $row_p = $res_precio->fetch_assoc();
            $precio = floatval($row_p['precio']);
        }
    }
    $subtotal = $precio * $cantidad;

    if ($accion == 'actualizar') {
        $check_query = "SELECT id FROM detalle_pedido WHERE pedido_id = $pedido_id AND producto_id = $producto_id";
        $result = $conexion->query($check_query);

        if ($result && $result->num_rows > 0) {
            $update_query = "UPDATE detalle_pedido SET cantidad = $cantidad, subtotal = $subtotal WHERE pedido_id = $pedido_id AND producto_id = $producto_id";
            $conexion->query($update_query);
        } else {
            $insert_query = "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, subtotal) VALUES ($pedido_id, $producto_id, $cantidad, $subtotal)";
            $conexion->query($insert_query);
        }
        echo json_encode(["status" => "success"]);
        exit;
    } 
    elseif ($accion == 'eliminar') {
        $delete_query = "DELETE FROM detalle_pedido WHERE pedido_id = $pedido_id AND producto_id = $producto_id";
        $conexion->query($delete_query);
        echo json_encode(["status" => "success"]);
        exit;
    } 
    elseif ($accion == 'vaciar') {
        $clear_query = "DELETE FROM detalle_pedido WHERE pedido_id = $pedido_id";
        $conexion->query($clear_query);
        echo json_encode(["status" => "success"]);
        exit;
    }
    elseif ($accion == 'obtener') {
        $query_carrito = "SELECT dp.producto_id, dp.cantidad, p.nombre, p.precio, p.imagen1 
                          FROM detalle_pedido dp 
                          JOIN productos p ON dp.producto_id = p.id 
                          WHERE dp.pedido_id = $pedido_id";
        
        $res = $conexion->query($query_carrito);
        $productos = [];
        
        if ($res && $res->num_rows > 0) {
            while ($fila = $res->fetch_assoc()) {
                $productos[] = [
                    'id' => intval($fila['producto_id']),
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
echo json_encode(["status" => "error"]);
?>