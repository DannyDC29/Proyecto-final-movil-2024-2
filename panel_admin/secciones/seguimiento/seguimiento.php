<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../../index.php");
    exit();
}

include('../../conexion.php');

// Manejar la eliminación de un seguimiento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Obtener información del seguimiento antes de eliminarlo para la bitácora
    $query_seguimiento_info = "
        SELECT s.descripcion, s.fecha, s.Terapia_idTerapia, u.nombre AS cliente_nombre, u.apellido AS cliente_apellido
        FROM seguimiento s
        INNER JOIN terapia t ON s.Terapia_idTerapia = t.idTerapia
        INNER JOIN cliente c ON t.Cliente_cliente_id = c.cliente_id
        INNER JOIN user u ON c.User_usuario_id = u.usuario_id
        WHERE s.seguimiento_id = $delete_id
        ORDER BY s.seguimiento_id DESC
    ";
    $result_seguimiento_info = mysqli_query($con, $query_seguimiento_info);
    $seguimiento_info = mysqli_fetch_assoc($result_seguimiento_info);

    if ($seguimiento_info) {
        // Eliminar el seguimiento
        $query_delete_seguimiento = "DELETE FROM seguimiento WHERE seguimiento_id = $delete_id";
        $result_delete_seguimiento = mysqli_query($con, $query_delete_seguimiento);

        if ($result_delete_seguimiento) {
            // Registrar la acción en la bitácora
            $admin_user_id = $_SESSION['user_id']; // ID del administrador actual
            $descripcion_bitacora = "Se eliminó el seguimiento ID: {$delete_id}, Descripción: {$seguimiento_info['descripcion']}, Fecha: {$seguimiento_info['fecha']}, Terapia ID: {$seguimiento_info['Terapia_idTerapia']}, Cliente: {$seguimiento_info['cliente_nombre']} {$seguimiento_info['cliente_apellido']}.";
            $query_insert_bitacora = "INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Admin_admin_id) 
                                      VALUES ('Eliminar', 'seguimiento', NOW(), '$descripcion_bitacora', 
                                      (SELECT admin_id FROM admin WHERE User_usuario_id = $admin_user_id))";
            mysqli_query($con, $query_insert_bitacora);

            echo "<script>alert('Seguimiento eliminado exitosamente'); window.location.href='seguimiento.php';</script>";
        } else {
            echo "<script>alert('Error al eliminar el seguimiento: " . mysqli_error($con) . "'); window.location.href='seguimiento.php';</script>";
        }
    } else {
        echo "<script>alert('No se encontró información sobre el seguimiento'); window.location.href='seguimiento.php';</script>";
    }
}

// Consulta para obtener la información de los seguimientos junto con el nombre del cliente
$query_seguimientos = "
    SELECT s.seguimiento_id, s.descripcion, s.fecha, s.Terapia_idTerapia, s.foto_seguimiento,
           u.nombre AS nombre_cliente, u.apellido AS apellido_cliente
    FROM seguimiento s
    INNER JOIN terapia t ON s.Terapia_idTerapia = t.idTerapia
    INNER JOIN cliente c ON t.Cliente_cliente_id = c.cliente_id
    INNER JOIN user u ON c.User_usuario_id = u.usuario_id
";

$result_seguimientos = mysqli_query($con, $query_seguimientos);
if (!$result_seguimientos) {
    die("Error en la consulta de seguimientos: " . mysqli_error($con));
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Seguimientos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, whitesmoke, #17b09e);
            font-family: 'Roboto', sans-serif;
            color: #333;
        }

        /* Barra de navegación */
        .navbar {
            background-color: white;
            padding: 1rem 2rem;
        }

        .navbar-brand img {
            width: 100px; /* Ajusta el tamaño del logo aquí */
        }

        .navbar-nav .nav-link {
            color: #1b185c !important;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: color 0.3s, background-color 0.3s;
            border-radius: 8px;
        }

        .navbar-nav .nav-link:hover {
            background-color: #17b09e !important;
            color: #fff !important;
        }

        /* Contenedor principal */
        .container {
            margin-top: 40px;
        }

        h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1b185c;
            text-align: center;
            margin-bottom: 30px;
        }

        /* Estilo de la tabla */
        .table {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .table th {
            background-color: #1b185c;
            color: white;
            font-weight: bold;
        }

        .table td, .table th {
            text-align: center;
            vertical-align: middle;
        }

        .table tbody tr {
            background-color: white;
        }

        /* Botones */
        .btn-primary {
            background-color: #1b185c;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-primary:hover {
            background-color: #1b185c;
            transform: scale(1.05);
        }

        .btn-danger {
            background-color: white;
            color: #c82333;
            font-weight: bold;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-danger:hover {
            background-color: #c82333;
            color: white;
        }

        /* Estilo para la imagen dentro de los botones */
        .icono-btn {
            width: 20px;  /* Ajusta el tamaño según lo necesites */
            height: 20px;
            margin-right: 8px;  /* Espacio entre la imagen y el texto */
            vertical-align: middle;  /* Alineación vertical */
        }
    </style>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="../../index1.php">
            <img src="logoat.jpg" alt="Logo"> <!-- Asegúrate de reemplazar "logoat.jpg" con la ruta correcta de tu logo -->
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="../../index1.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/user/user.php">Usuarios</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/cliente/cliente.php">Clientes</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/animal/animal.php">Animales</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/terapia/terapia.php">Terapias</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/seguimiento/seguimiento.php">Seguimientos</a></li>
                <li class="nav-item"><a class="nav-link" href="../../secciones/bitacora/bitacora.php">Bitácora</a></li>
                <li class="nav-item"><a class="nav-link" href="../../logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container mt-5">
        <h1 class="text-center">Lista de Seguimientos</h1>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Seguimiento</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Terapia ID</th>
                    <th>Cliente</th>
                    <th>Foto</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_seguimiento = mysqli_fetch_assoc($result_seguimientos)): ?>
                    <tr>
                        <td><?php echo $row_seguimiento['seguimiento_id']; ?></td>
                        <td><?php echo $row_seguimiento['descripcion']; ?></td>
                        <td><?php echo $row_seguimiento['fecha']; ?></td>
                        <td><?php echo $row_seguimiento['Terapia_idTerapia']; ?></td>
                        <td><?php echo $row_seguimiento['nombre_cliente'] . ' ' . $row_seguimiento['apellido_cliente']; ?></td>
                        <td>
                            <?php if ($row_seguimiento['foto_seguimiento']): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($row_seguimiento['foto_seguimiento']); ?>" alt="Foto del seguimiento" width="80">
                            <?php else: ?>
                                Sin foto
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $row_seguimiento['seguimiento_id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar este seguimiento?');">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
