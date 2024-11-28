<?php
session_start();
if (!isset($_SESSION['admin']) && !isset($_SESSION['especialista'])) {
    header('Location: ../../index.php');
    exit();
}
include('../../conexion.php');

$id = $_GET['id'];
$mensaje = '';

// Obtener datos de la terapia
$query_terapia = "SELECT * FROM terapia WHERE idTerapia = $id";
$result_terapia = mysqli_query($con, $query_terapia);
if (!$result_terapia) {
    die("Error en la consulta de la terapia: " . mysqli_error($con));
}

$terapia = mysqli_fetch_assoc($result_terapia);

// Función para convertir la fecha al formato que requiere 'datetime-local'
function formatDateTime($dateTime)
{
    $date = new DateTime($dateTime);
    return $date->format('Y-m-d\TH:i');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $estado = $_POST['estado'];
    $notas = $_POST['notas'];


    // Actualizar datos de la terapia
    $query_update_terapia = "UPDATE terapia SET fecha_inicio='$fecha_inicio', fecha_fin='$fecha_fin', estado='$estado', notas='$notas', experiencia='$experiencia' WHERE idTerapia=$id";
    $result_update_terapia = mysqli_query($con, $query_update_terapia);

    if ($result_update_terapia) {
        // Registro en la bitácora
        $admin_user_id = $_SESSION['user_id']; // ID del administrador actual
        $descripcion = "Se actualizó la terapia con ID: $id. Nuevos valores: Fecha Inicio: $fecha_inicio, Fecha Fin: $fecha_fin, Estado: $estado, Notas: $notas, Experiencia: $experiencia.";
        $query_insert_bitacora = "INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Admin_admin_id) 
                                  VALUES ('Actualizar', 'terapia', NOW(), '$descripcion', 
                                  (SELECT admin_id FROM admin WHERE User_usuario_id = $admin_user_id))";
        mysqli_query($con, $query_insert_bitacora);

        echo "<script>alert('Terapia actualizada exitosamente'); window.location.href='terapia.php';</script>";
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar la terapia: " . mysqli_error($con) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Terapia</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, whitesmoke, #17b09e);
            font-family: 'Roboto', sans-serif;
            color: #333;
        }

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

        .container {
            margin-top: 50px;
            max-width: 600px;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #1b185c;
            color: white;
            font-weight: bold;
            font-size: 1.5rem;
            text-align: center;
        }

        .btn-primary {
            background-color: #1b185c;
            border: none;
            transition: background-color 0.3s, transform 0.3s;
        }

        .btn-primary:hover {
            background-color: #17b09e;
            transform: scale(1.05);
        }

        /* Botón regresar con fondo blanco y borde */
        .btn-regresar {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            font-size: 16px;
            color: #1b185c;
            background-color: #1b185c;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            border: 2px solid;
            transition: background-color 0.3s, color 0.3s;
            margin-right: 10px;
        }

        .btn-regresar img {
            width: 20px;
            height: 20px;
            margin-right: 8px;
        }

        .btn-regresar:hover {
            background-color: #17b09e;
            color: #17b09e;
        }

        .form-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="../../index1.php">
            <img src="logoat.jpg" alt="Logo">
        </a>
    </nav>
    <div class="container">
        <div class="card">
            <div class="card-header">Editar Terapia</div>
            <div class="card-body">
                <?php if ($mensaje)
                    echo $mensaje; ?>
                <form method="post">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha Inicio:</label>
                        <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio"
                            value="<?php echo formatDateTime($terapia['fecha_inicio']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="fecha_fin">Fecha Fin:</label>
                        <input type="datetime-local" class="form-control" id="fecha_fin" name="fecha_fin"
                            value="<?php echo formatDateTime($terapia['fecha_fin']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="activo" <?php if ($terapia['estado'] == 'activo')
                                echo 'selected'; ?>>Activo
                            </option>
                            <option value="finalizado" <?php if ($terapia['estado'] == 'finalizado')
                                echo 'selected'; ?>>
                                Finalizado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notas">Notas:</label>
                        <textarea class="form-control" id="notas"
                            name="notas"><?php echo $terapia['notas']; ?></textarea>
                    </div>

                    <div class="form-buttons">
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        <a href="terapia.php" class="btn-regresar">
                            <img src="volver.png" alt="">
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    

    <br><br><br>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>