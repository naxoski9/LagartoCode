<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofertas - Lagarto Grow</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            display: flex;
            height: 100vh;
        }

        .container {
            display: flex;
            width: 100%;
        }

        .sidebar {
            background-color: #f4f4f4;
            width: 200px;
            padding: 15px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
        }

        .sidebar .user-section {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .sidebar .user-section .user-icon {
            font-size: 24px;
            margin-right: 10px;
        }

        .sidebar nav {
            margin-bottom: 20px;
        }

        .sidebar nav ul {
            list-style: none;
            padding: 0;
        }

        .sidebar nav ul li {
            margin: 15px 0;
        }

        .sidebar nav ul li button {
            width: 100%;
            padding: 10px;
            border: none;
            background-color: #D5DBDB;
            cursor: pointer;
            text-align: left;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            font-size: 16px;
            color: black;
        }

        .sidebar nav ul li button:hover {
            background-color: #AAB7B8;
        }

        .logout-button {
            width: 100%;
            padding: 10px;
            background-color: #ff4f4f;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .main-content {
            flex-grow: 1;
            background-color: #f9f9f9;
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
        }

        .product-list {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: center;
        }

        .card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            width: 220px;
            background-color: white;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card img {
            width: 120px;
            height: 120px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .card h2 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .card p {
            font-size: 14px;
            margin-bottom: 5px;
        }

        .add-offer-section {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .add-offer-btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .add-offer-btn:hover {
            background-color: #218838;
        }

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
    <div class="container">
        <aside class="sidebar">
            <div class="user-section">
                <span class="user-icon">👤</span>
                <p>jefe</p>
            </div>
            <nav class="menu">
                <ul>
                    <li><button onclick="location.href='Inventario.php'">📦 Inventario</button></li>
                    <li><button onclick="location.href='Proveedores.php'">🛒 Proveedores</button></li>
                    <li><button onclick="location.href='Seguimiento.html'">📊 Seguimiento</button></li>
                    <li><button onclick="location.href='Boleta.php'">🧾 Historial ventas</button></li>
                </ul>
            </nav>
            <button class="logout-button" onclick="location.href='../login.php'">Cerrar Sesión</button>
        </aside>

        <main class="main-content">
            <header class="header">
                <img src="../img/lagarto.jpg" alt="Lagarto Grow Logo" class="logo">
            </header>

            <div class="offer-container">
                <h2 class="offer-title">Ofertas actuales</h2>
                <div class="product-list" id="offerList">
                    <?php
                    function formatPrice($price) {
                        return '$' . number_format((float)$price, 0, ',', '.');
                    }

                    function createOfferCard($row) {
                        $card = '<div class="card">';
                        $card .= '<h2>' . $row["nombre_producto"] . '</h2>';
                        $card .= '<p><strong>Precio Original:</strong> ' . formatPrice($row["precio_original"]) . '</p>';
                        $card .= '<p><strong>Precio en Oferta:</strong> ' . formatPrice($row["precio_oferta"]) . '</p>';
                        $card .= '<p><strong>Estado:</strong> ' . $row["estado"] . '</p>';
                        $card .= '<p><strong>Proveedor:</strong> ' . $row["nombre_proveedor"] . '</p>';
                        $card .= '<p><strong>Descripción:</strong> ' . $row["descripcion"] . '</p>';

                        if (!empty($row["imagen_url"])) {
                            $card .= '<img src="' . $row["imagen_url"] . '" alt="' . $row["nombre_producto"] . '">';
                        } else {
                            $card .= '<p>Sin imagen disponible</p>';
                        }

                        $card .= '</div>';
                        return $card;
                    }

                    $host = 'localhost';
                    $user = 'feriasof1_grupo3';
                    $password = 'RY9jaepMgPmTP6gEQKbM';
                    $dbname = 'feriasof1_grupo3';

                    $conn = new mysqli($host, $user, $password, $dbname);

                    if ($conn->connect_error) {
                        die("Error de conexión: " . $conn->connect_error);
                    }

                    $sql = "
                        SELECT p.descripcion, p.nombre_producto, p.precio AS precio_original, o.precio_oferta, p.estado, p.imagen_url, pr.nombre AS nombre_proveedor
                        FROM ofertas o
                        JOIN producto p ON o.producto_id = p.id
                        JOIN proveedores pr ON p.proveedor_id = pr.id
                    ";

                    $result = $conn->query($sql);
                    $offers = [];

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $offers[] = $row;
                        }
                    } else {
                        echo '<p class="no-offers">No hay ofertas disponibles.</p>';
                    }

                    foreach ($offers as $row) {
                        echo createOfferCard($row);
                    }

                    $conn->close();
                    ?>
                </div>

                <div class="add-offer-section">
                    <button class="add-offer-btn" onclick="location.href='Agregar_Oferta.php'">Agregar nueva oferta</button>
                    <button type="submit"class="add-offer-btn" name="agregar" onclick="window.history.back()">Volver atras</button>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
