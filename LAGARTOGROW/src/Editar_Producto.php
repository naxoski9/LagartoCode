<?php

$host = 'localhost';
$db = 'lagartogrow_db'; 
$user = 'root';
$password = ''; 

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$producto = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['codigo'])) {
    $codigo = $_POST['codigo']; 

    $stmt = $conn->prepare("SELECT p.*, pr.nombre AS nombre_proveedor FROM producto p 
        JOIN proveedores pr ON p.proveedor_id = pr.id 
        WHERE codigo = ?");
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
    } else {
        die("Producto no encontrado.");
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nombre_producto']) && $producto !== null) {
    $id = $producto['id'];
    $nombre_producto = $_POST['nombre_producto'];
    $stock = $_POST['stock'];
    $precio = $_POST['precio'];
    $estado = $_POST['estado'];
    $descripcion = $_POST['descripcion'];
    $proveedor_id = $_POST['proveedor_id'];

    // Manejo de la imagen subida
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Verificar si es una imagen real
        $check = getimagesize($_FILES["imagen"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "El archivo no es una imagen.";
            $uploadOk = 0;
        }

        // Subir la imagen
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
                // Actualizar el producto con la nueva imagen
                $stmt = $conn->prepare("UPDATE producto SET nombre_producto=?, stock=?, precio=?, estado=?, descripcion=?, proveedor_id=?, imagen_url=? WHERE id=?");
                $stmt->bind_param("sidssssi", $nombre_producto, $stock, $precio, $estado, $descripcion, $proveedor_id, $target_file, $id);
            } else {
                echo "Lo siento, hubo un error al subir tu archivo.";
            }
        }
    } else {
        // Actualizar sin cambiar la imagen
        $stmt = $conn->prepare("UPDATE producto SET nombre_producto=?, stock=?, precio=?, estado=?, descripcion=?, proveedor_id=? WHERE id=?");
        $stmt->bind_param("sidssii", $nombre_producto, $stock, $precio, $estado, $descripcion, $proveedor_id, $id);
    }

    if ($stmt->execute()) {
        echo "<p>Producto actualizado correctamente.</p>";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

$proveedores = [];
$result = $conn->query("SELECT id, nombre FROM proveedores");
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $proveedores[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/editar.css">
    <title>Editar Producto</title>
    <style>
    .button-s {
        background-color: #ddd;
        color: black;
        border: none;
        padding: 10px 20px;
        font-size: 16px;
        cursor: pointer;
        margin-top: 20px;
        display: block;
        width: 218px;
        margin-left: auto;
        margin-right: auto;
    }

    .button-s:hover {
        background-color: #bbb;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        margin-top: 100px;
    }

    .logo {
        width: 100px;
        height: auto;
        position: absolute;
        top: 10px;
        right: 10px;
    }

    input[type="file"] {
        display: none;
    }

    .custom-file-upload {
        display: center;
        padding: 10px;
        cursor: pointer;
        background-color: #28a745;
        color: white;
        border-radius: 8px;
        text-align: center;
        transition: background-color 0.3s, transform 0.3s;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin: 10px 0;
        width: 120%;
        max-width: 220px;
        margin-left: auto;
        margin-right: auto;
    }

    .custom-file-upload:hover {
        background-color: #218838;
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
    }

    .custom-file-upload:active {
        background-color: #1e7e34;
        transform: scale(0.98);
        box-shadow: 0 3px 5px rgba(0, 0, 0, 0.2);
    }
    </style>
</head>

<body>

    <div class="container">
        <div class="main-content">
            <div class="search-bar-container">
                <h1>Editar Producto</h1>
                <div class="search-bar">
                    <form action="" method="POST">
                        <input type="text" id="codigo" name="codigo" placeholder="Buscar producto..." required>
                        <button type="submit">Buscar</button>
                    </form>
                </div>
            </div>

            <?php if ($producto !== null): ?>
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($producto['codigo']); ?>">

                <label for="nombre_producto">Nombre del producto:</label>
                <input type="text" id="nombre_producto" name="nombre_producto"
                    value="<?php echo htmlspecialchars($producto['nombre_producto']); ?>" required>

                <label for="stock">Stock:</label>
                <input type="number" id="stock" name="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>"
                    required>

                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio"
                    value="<?php echo htmlspecialchars($producto['precio']); ?>" step="0.01" required>

                <label for="estado">Estado:</label>
                <select id="estado" name="estado" required>
                    <option value="disponible"
                        <?php echo (isset($producto['estado']) && $producto['estado'] == 'disponible') ? 'selected' : ''; ?>>
                        Disponible</option>
                    <option value="agotado"
                        <?php echo (isset($producto['estado']) && $producto['estado'] == 'agotado') ? 'selected' : ''; ?>>
                        Agotado</option>
                    <option value="descontinuado"
                        <?php echo (isset($producto['estado']) && $producto['estado'] == 'descontinuado') ? 'selected' : ''; ?>>
                        Descontinuado</option>
                </select>

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"
                    required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>

                <label for="proveedor_id">Proveedor:</label>
                <select id="proveedor_id" name="proveedor_id" required>
                    <?php foreach ($proveedores as $proveedor): ?>
                    <option value="<?php echo $proveedor['id']; ?>"
                        <?php echo ($producto['proveedor_id'] == $proveedor['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($proveedor['nombre']); ?>
                    </option>
                    <?php endforeach; ?>
                </select>

                <label for="imagen" class="upload-label">Seleccionar archivo</label>
                <input type="file" id="imagen" name="imagen" accept="image/*" class="custom-file-upload"><br>

                <button type="submit">Actualizar Producto</button>
            </form>
            <?php endif; ?>

            <div>
                <button class="button-s" onclick="location.href='inventario.php'">Volver al Inventario</button>
            </div>

        </div>
    </div>
</body>

</html>