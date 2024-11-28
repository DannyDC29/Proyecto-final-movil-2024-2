<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: ../../index.php");
    exit();
}

include('../../conexion.php');

// Consulta para obtener la información de las bitácoras junto con el nombre del administrador o especialista
$query_bitacoras = "
    SELECT b.bitacora_id, b.accion, b.entidad, b.fecha_hora, b.descripcion,
           u.nombre AS nombre_usuario, u.apellido AS apellido_usuario,
           CASE 
               WHEN b.Admin_admin_id IS NOT NULL THEN 'Admin'
               WHEN b.Especialista_especialista_id IS NOT NULL THEN 'Especialista'
               ELSE 'Desconocido'
           END AS rol_usuario
    FROM bitacora b
    LEFT JOIN admin a ON b.Admin_admin_id = a.admin_id
    LEFT JOIN especialista e ON b.Especialista_especialista_id = e.especialista_id
    LEFT JOIN user u ON (a.User_usuario_id = u.usuario_id OR e.User_usuario_id = u.usuario_id)
    ORDER BY b.fecha_hora DESC
";

$result_bitacoras = mysqli_query($con, $query_bitacoras);
if (!$result_bitacoras) {
    die("Error en la consulta de bitácoras: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Bitácora</title>
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
            width: 100px;
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

    <div class="container mt-5">
        <h1 class="text-center">Lista de Bitácoras</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Bitácora</th>
                    <th>Acción</th>
                    <th>Entidad</th>
                    <th>Fecha y Hora</th>
                    <th>Descripción</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_bitacora = mysqli_fetch_assoc($result_bitacoras)): ?>
                    <tr>
                        <td><?php echo $row_bitacora['bitacora_id']; ?></td>
                        <td><?php echo $row_bitacora['accion']; ?></td>
                        <td><?php echo $row_bitacora['entidad']; ?></td>
                        <td><?php echo $row_bitacora['fecha_hora']; ?></td>
                        <td><?php echo $row_bitacora['descripcion']; ?></td>
                        <td><?php echo $row_bitacora['nombre_usuario'] . ' ' . $row_bitacora['apellido_usuario']; ?></td>
                        <td><?php echo $row_bitacora['rol_usuario']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
