<?php
$host = 'localhost';
$user = 'feriasof1_grupo3'; 
$password = 'RY9jaepMgPmTP6gEQKbM';
$dbname = 'feriasof1_grupo3';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Error de conexión: ' . $conn->connect_error]));
}

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $sql = "SELECT * FROM producto WHERE codigo = '$codigo'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Producto no encontrado']);
    }
}

$conn->close();
?>
