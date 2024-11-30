<?php
$host = 'localhost';
$user = 'feriasof1_grupo3';
$password = 'RY9jaepMgPmTP6gEQKbM';
$dbname = 'feriasof1_grupo3';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $producto_id = $_POST['producto_id'];
    $precio_original = $_POST['precio_original'];
    $precio_oferta = $_POST['precio_oferta'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    $sql = "INSERT INTO ofertas (producto_id, precio_original, precio_oferta, fecha_inicio, fecha_fin)
            VALUES ('$producto_id', '$precio_original', '$precio_oferta', '$fecha_inicio', '$fecha_fin')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>Oferta agregada exitosamente</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT id, nombre_producto FROM producto";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Oferta - Lagarto Grow</title>
    <style>
        .form-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            margin-top: 20px;
            padding: 10px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Agregar Oferta</h1>
        <form method="POST" action="Agregar_Oferta.php">
            <label for="producto_id">Producto:</label>
            <select name="producto_id" id="producto_id" required>
                <option value="">Seleccionar Producto</option>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<option value="' . $row["id"] . '">' . $row["nombre_producto"] . '</option>';
                    }
                }
                ?>
            </select>

            <label for="precio_original">Precio Original:</label>
            <input type="number" step="0.01" name="precio_original" id="precio_original" required>

            <label for="precio_oferta">Precio Oferta:</label>
            <input type="number" step="0.01" name="precio_oferta" id="precio_oferta" required>

            <label for="fecha_inicio">Fecha Inicio:</label>
            <input type="date" name="fecha_inicio" id="fecha_inicio" required>

            <label for="fecha_fin">Fecha Fin:</label>
            <input type="date" name="fecha_fin" id="fecha_fin" required>

            <button type="submit">Agregar Oferta</button>
            <button type="submit" name="agregar" onclick="window.history.back()">Volver atras</button>
        </form>
    </div>
</body>
</html>

<?php
$conn->close();
?>
