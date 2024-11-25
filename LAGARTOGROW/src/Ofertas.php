<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofertas y Promociones - Lagarto Grow</title>
    <link rel="stylesheet" href="../css/ofertas.css">
</head>

<body>
    <div class="container">
        <aside class="sidebar">
            <div class="user-section">
                <span class="user-icon">👤</span>
                <p>Admin</p>
            </div>
            <nav class="menu">
                <ul>
                    <li><button onclick="location.href='inventario.php'">📦 Inventario</button></li>
                    <li><button onclick="location.href='proveedores.php'">🛒 Proveedores</button></li>
                    <li><button onclick="location.href='seguimiento.html'">📊 Seguimiento</button></li>
                    <li><button onclick="location.href='boleta.php'">🧾 Historial ventas</button></li>
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
                    $db = 'lagartogrow_db';
                    $user = 'root';
                    $password = '';
                    $conn = new mysqli($host, $user, $password, $db);

                    if ($conn->connect_error) {
                        die("Error de conexión: " . $conn->connect_error);
                    }

                    $sql = "
                        SELECT p.descripcion, p.nombre_producto, p.precio AS precio_original, o.precio_oferta, p.estado, p.imagen_url, pr.nombre AS nombre_proveedor
                        FROM ofertas o
                        JOIN producto p ON o.producto_id = p.id
                        JOIN proveedores pr ON p.proveedor_id = pr.id
                        WHERE CURDATE() BETWEEN o.fecha_inicio AND o.fecha_fin
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
                    <button class="add-offer-btn" onclick="location.href='Agregar_Oferta.php'">Agregar nueva
                        oferta</button>
                </div>
            </div>
        </main>
    </div>
</body>

</html>