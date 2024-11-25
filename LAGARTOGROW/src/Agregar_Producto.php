<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto</title>
    <link rel="stylesheet" href="../css/agregar.css">
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
    <img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">

    <div class="form-container">
        <h1>Agregar nuevo producto</h1>

        <?php
        $host = 'localhost';
        $db = 'lagartogrow_db';
        $user = 'root';
        $password = '';

        $conn = new mysqli($host, $user, $password, $db);

        if ($conn->connect_error) {
            die("Error de conexión: " . $conn->connect_error);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $nombre_producto = $_POST['nombre_producto'];
            $codigo = $_POST['codigo'];
            $stock = $_POST['stock'];
            $precio = $_POST['precio'];
            $estado = $_POST['estado'];
            $descripcion = $_POST['descripcion'];
            $proveedor_id = $_POST['proveedor_id'];

            $target_dir = "../uploads/";

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            $target_file = $target_dir . basename($_FILES["imagen"]["name"]);
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            $check = getimagesize($_FILES["imagen"]["tmp_name"]);
            if ($check !== false) {
                $uploadOk = 1;
            } else {
                echo "El archivo no es una imagen.";
                $uploadOk = 0;
            }

            if (file_exists($target_file)) {
                echo "Lo siento, el archivo ya existe.";
                $uploadOk = 0;
            }

            if ($_FILES["imagen"]["size"] > 5000000) {
                echo "Lo siento, tu archivo es demasiado grande.";
                $uploadOk = 0;
            }

            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                echo "Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF.";
                $uploadOk = 0;
            }

            if ($uploadOk == 1) {
                if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file)) {
                    $imagen_url = $target_file;

                    $sql = "INSERT INTO producto (nombre_producto, codigo, stock, precio, estado, descripcion, proveedor_id, imagen_url) 
                            VALUES ('$nombre_producto', '$codigo', $stock, $precio, '$estado', '$descripcion', '$proveedor_id', '$imagen_url')";

                    if ($conn->query($sql) === TRUE) {
                        header("Location: inventario.php");
                        exit();
                    } else {
                        echo "Error: " . $sql . "<br>" . $conn->error;
                    }
                } else {
                    echo "Lo siento, hubo un error al subir tu archivo.";
                }
            }
        }

        $sql = "SELECT id, nombre FROM proveedores";
        $result = $conn->query($sql);
        ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label for="nombre_producto">Nombre del producto:</label>
            <input type="text" id="nombre_producto" name="nombre_producto" required><br>

            <label for="codigo">Código del producto:</label>
            <input type="text" id="codigo" name="codigo" required><br>

            <label for="stock">Stock:</label>
            <input type="number" id="stock" name="stock" required><br>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" step="0.01" required><br>

            <label for="estado">Estado:</label>
            <select id="estado" name="estado" required>
                <option value="disponible">Disponible</option>
                <option value="agotado">Agotado</option>
                <option value="descontinuado">Descontinuado</option>
            </select><br>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea><br>

            <label for="proveedor_id">Proveedor:</label>
            <select id="proveedor_id" name="proveedor_id" required>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['nombre'] . "</option>";
                    }
                } else {
                    echo "<option>No hay proveedores disponibles</option>";
                }
                ?>
            </select><br>
            <label for="imagen" class="custom-file-upload">Seleccionar Imagen</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" required>

            <button type="submit">Agregar Producto</button>

        </form>


    </div>

    <?php
    $conn->close();
    ?>
    <div>
        <button class="button-s" onclick="location.href='inventario.php'">Volver al Inventario</button>
    </div>
</body>

</html>
<br>