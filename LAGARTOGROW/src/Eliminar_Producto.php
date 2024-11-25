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
    SELECT p.id, p.nombre_producto, p.codigo, p.stock, p.precio, p.estado, p.descripcion, pr.nombre AS nombre_proveedor 
    FROM producto p 
    JOIN proveedores pr ON p.proveedor_id = pr.id
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Producto</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>

<body>
    <h1>Eliminar producto</h1>
    <table>
        <tr>
            <th>Nombre</th>
            <th>Código</th>
            <th>Stock</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Descripción</th>
            <th>Proveedor</th>
            <th>Acción</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['nombre_producto']; ?></td>
            <td><?php echo $row['codigo']; ?></td>
            <td><?php echo $row['stock']; ?></td>
            <td><?php echo '$' . $row['precio']; ?></td>
            <td><?php echo $row['estado']; ?></td>
            <td><?php echo $row['descripcion']; ?></td>
            <td><?php echo $row['nombre_proveedor']; ?></td>
            <td>
                <form action="eliminar_producto.php" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="button"
                        onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?');">Eliminar</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <?php
    
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
        $id = $_POST['id'];

        $delete_sql = "DELETE FROM producto WHERE id = ?";
        
        $stmt = $conn->prepare($delete_sql);
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo "<p>Producto eliminado exitosamente.</p>";
          
            $result = $conn->query($sql);
        } else {
            echo "<p>Error al eliminar el producto: " . $conn->error . "</p>";
        }

        $stmt->close();
    }
    ?>

    <button onclick="location.href='inventario.php'">Volver al Inventario</button>

    <?php

    $conn->close();
    ?>
</body>

</html>
