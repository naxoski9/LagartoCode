<?php
$host = 'localhost';
$db = 'lagartogrow_db'; 
$user = 'root';  
$password = '';  

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$sql = "
    SELECT id, nombre, email, direccion, telefono 
    FROM proveedores
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Proveedor</title>
    <link rel="stylesheet" href="../css/opciones.css">
    <img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">
    <style>
    .logo {
        width: 100px;
        height: auto;
        position: absolute;
        top: 10px;
        right: 10px;
    }
    </style>
</head>

<body>
    <h1>Eliminar proveedor</h1>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Teléfono</th>
            <th>Dirección</th>
            <th>Acción</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['nombre']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo htmlspecialchars($row['telefono']); ?></td>
            <td><?php echo htmlspecialchars($row['direccion']); ?></td>
            <td>
                <form action="eliminar_proveedor.php" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                    <button type="submit" class="button"
                        onclick="return confirm('¿Estás seguro de que deseas eliminar este proveedor?');">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
        $id = $_POST['id'];

        $delete_sql = "DELETE FROM proveedores WHERE id = ?";
        
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<p>Proveedor eliminado exitosamente.</p>";
            $result = $conn->query($sql);
        } else {
            echo "<p>Error al eliminar el proveedor: " . $conn->error . "</p>";
        }

        $stmt->close();
    }
    ?>

    <button onclick="location.href='agregar_proveedor.php'">Agregar Proveedor</button>
    <button onclick="location.href='inventario.php'">Volver al Inventario</button>

    <?php
    $conn->close();
    ?>
</body>

</html>