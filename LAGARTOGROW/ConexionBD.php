<?php

$host = 'localhost'; 
$dbname = 'lagartogrow_db'; 
$username = 'root'; 
$password = ''; 

try {
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
 
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    echo "Conexión exitosa a la base de datos: " . $dbname;
} catch (PDOException $e) {
    
    echo "Error al conectar con la base de datos: " . $e->getMessage();
}
?>
