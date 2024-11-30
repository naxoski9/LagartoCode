<?php
include '../conexionBD.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo_seguimiento = isset($_POST['codigo_seguimiento']) ? $_POST['codigo_seguimiento'] : '';
    $producto_id = isset($_POST['producto_id']) ? $_POST['producto_id'] : '';
    $estado = isset($_POST['estado']) ? $_POST['estado'] : '';
    $cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : '';

    if (empty($codigo_seguimiento) || empty($producto_id) || empty($estado) || empty($cantidad)) {
        echo "Todos los campos son obligatorios.";
        exit;
    }

    $query = "SELECT * FROM pedidos WHERE codigo_seguimiento = '$codigo_seguimiento'";
    $result = $conn->query($query);

    if ($result === false) {
        echo "Error en la consulta: " . $conn->error;
        exit;
    }

    if ($result->num_rows > 0) {
        echo "El código de seguimiento ya existe. Por favor, genere un código diferente.";
    } else {
        $fecha = date('Y-m-d');
        $precio_total = 0.00;

        $query = "INSERT INTO pedidos (codigo_seguimiento, fecha, estado, precio_total) 
                  VALUES ('$codigo_seguimiento', '$fecha', '$estado', '$precio_total')";

        if ($conn->query($query) === TRUE) {
            $pedido_id = $conn->insert_id;

            $precio_producto = 0.00;

            $query = "INSERT INTO pedido_producto (pedido_id, producto_id, cantidad, precio) 
                      VALUES ('$pedido_id', '$producto_id', '$cantidad', '$precio_producto')";

            if ($conn->query($query) === TRUE) {
                echo "Pedido agregado correctamente.";
            } else {
                echo "Error al agregar el pedido-producto: " . $conn->error;
            }
        } else {
            echo "Error al agregar el pedido: " . $conn->error;
        }
    }

    $conn->close();
}
?>
