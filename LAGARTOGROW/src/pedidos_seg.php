<?php
$host = 'localhost';
$dbname = 'lagartogrow_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "Error de conexión: " . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['codigo_seguimiento'], $_POST['estado'], $_POST['producto'])) {
    $codigo = $_POST['codigo_seguimiento'];
    $estado = $_POST['estado'];
    $producto = $_POST['producto'];
    $fecha = date("Y-m-d H:i:s");

    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM pedidos_seg WHERE codigo_seguimiento = ?");
        $stmt->execute([$codigo]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $stmt = $pdo->prepare("UPDATE pedidos_seg SET estado = ?, fecha = ?, nombre_producto = ? WHERE codigo_seguimiento = ?");
            $stmt->execute([$estado, $fecha, $producto, $codigo]);
            echo json_encode(["status" => "success", "message" => "Pedido actualizado correctamente"]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO pedidos_seg (codigo_seguimiento, fecha, estado, nombre_producto) VALUES (?, ?, ?, ?)");
            $stmt->execute([$codigo, $fecha, $estado, $producto]);
            echo json_encode(["status" => "success", "message" => "Pedido agregado correctamente"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error al agregar o actualizar el pedido: " . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar_codigo'])) {
    $codigo = $_POST['eliminar_codigo'];

    try {
        $stmt = $pdo->prepare("DELETE FROM pedidos_seg WHERE codigo_seguimiento = ?");
        $stmt->execute([$codigo]);
        echo json_encode(["status" => "success", "message" => "Pedido eliminado correctamente"]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error al eliminar el pedido: " . $e->getMessage()]);
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM pedidos_seg ORDER BY fecha DESC");
        $stmt->execute();
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($pedidos);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Error al obtener los pedidos: " . $e->getMessage()]);
    }
}
?>










