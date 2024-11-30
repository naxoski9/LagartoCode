<?php
$host = 'localhost';
$user = 'feriasof1_grupo3';
$password = 'RY9jaepMgPmTP6gEQKbM';
$dbname = 'feriasof1_grupo3';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['codigo'])) {
    $codigo = $_GET['codigo'];
    $sql = "SELECT id FROM producto WHERE codigo = '$codigo'";
    $result = $conn->query($sql);
    echo json_encode(["existe" => $result->num_rows > 0]);
}
$conn->close();
?>
