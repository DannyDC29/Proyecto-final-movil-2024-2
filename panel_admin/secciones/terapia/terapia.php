<?php
session_start();
if (!isset($_SESSION['admin']) && !isset($_SESSION['especialista'])) {
    header('Location: ../../index.php');
    exit();
}

include('../../conexion.php');

// Consulta para obtener toda la información de las terapias
$query_terapias = "
    SELECT t.idTerapia, t.fecha_inicio, t.fecha_fin, t.estado, t.notas, t.experiencia,
           COALESCE(c.nombre, 'No asignado') AS nombre_cliente,
           COALESCE(e.nombre, 'No asignado') AS nombre_especialista,
           COALESCE(a.nombre, 'No asignado') AS nombre_animal
    FROM terapia t
    LEFT JOIN cliente cl ON t.Cliente_cliente_id = cl.cliente_id
    LEFT JOIN user c ON cl.User_usuario_id = c.usuario_id
    LEFT JOIN especialista es ON t.Especialista_especialista_id = es.especialista_id
    LEFT JOIN user e ON es.User_usuario_id = e.usuario_id
    LEFT JOIN animal a ON t.Animal_animal_id = a.animal_id
    ORDER BY t.idTerapia DESC
";

$result_terapias = mysqli_query($con, $query_terapias);
if (!$result_terapias) {
    die("Error en la consulta de terapias: " . mysqli_error($con));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Terapias</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

        .icono-btn {
            width: 25px;  
            height: 25px;
            margin-right: 10px; 
            vertical-align: middle; 
        }

        .btn-warning {
            background-color: white;
            color: #333;
            font-weight: bold;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-warning:hover {
            background-color: #1b185c;
        }

        .btn-danger {
            background-color: white;
            color: white;
            font-weight: bold;
            border: none;
            transition: background-color 0.3s;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        /* Botón regresar */
        .btn-regresar {
            display: inline-flex;
            align-items: center;
            padding: 5px 15px;
            font-size: 14px;
            color: #fff;
            background-color: #1b185c;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
            margin-top: 10px;
        }

        .btn-regresar img {
            width: 16px;
            height: 16px;
            margin-right: 5px;
        }

        .btn-regresar:hover {
            background-color: #17b09e;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="../../index1.php">
            <img src="logoat.jpg" alt="Logo">
        </a>
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

    <div class="container">
        <h1>Lista de Terapias</h1>
        <a href="createTerapia.php" class="btn btn-primary mb-3">
            <img src="agregaruser.png" alt="" style="width: 20px; margin-right: 5px;">
        </a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Terapia</th>
                    <th>Cliente</th>
                    <th>Especialista</th>
                    <th>Animal</th>
                    <th>Fecha Inicio</th>
                    <th>Fecha Fin</th>
                    <th>Estado</th>
                    <th>Notas</th>
                    <th>Experiencia</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result_terapias)): ?>
                    <tr>
                        <td><?php echo $row['idTerapia']; ?></td>
                        <td><?php echo $row['nombre_cliente']; ?></td>
                        <td><?php echo $row['nombre_especialista']; ?></td>
                        <td><?php echo $row['nombre_animal']; ?></td>
                        <td><?php echo $row['fecha_inicio']; ?></td>
                        <td><?php echo $row['fecha_fin']; ?></td>
                        <td><?php echo ucfirst($row['estado']); ?></td>
                        <td><?php echo $row['notas'] ?: 'N/A'; ?></td>
                        <td><?php echo $row['experiencia'] ?: 'N/A'; ?></td>
                        <td>
                            <a href="updateTerapia.php?id=<?php echo $row['idTerapia']; ?>" class="btn btn-warning btn-sm">
                                <img src="editar.png" alt="" class="icono-btn">
                            </a>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?php echo $row['idTerapia']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que deseas eliminar esta terapia?');">
                                    <img src="borrar.png" alt="" class="icono-btn"> Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
