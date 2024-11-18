<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'lagartogrow_db';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $proveedor_id = $_GET['id'];
    $sql = "SELECT nombre, email FROM proveedores WHERE id = '$proveedor_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $proveedor = $result->fetch_assoc();
        echo json_encode($proveedor);
    } else {
        echo json_encode(['nombre' => '', 'email' => '']);
    }
}

$conn->close();
?>
