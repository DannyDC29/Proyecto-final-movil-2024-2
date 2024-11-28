<?php
session_start();
if (!isset($_SESSION['admin']) && !isset($_SESSION['especialista'])) {
    header('Location: ../../index.php');
    exit();
}

include('../../conexion.php');

$mensaje = ''; // Variable para mensajes de error

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $especialista_id = $_POST['especialista_id'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];
    $estado = $_POST['estado'];
    $notas = !empty($_POST['notas']) ? $_POST['notas'] : NULL;

    $query_cliente = "SELECT cliente_id FROM cliente WHERE cliente_id = ?";
    $stmt_cliente = mysqli_prepare($con, $query_cliente);
    mysqli_stmt_bind_param($stmt_cliente, 'i', $cliente_id);
    mysqli_stmt_execute($stmt_cliente);
    $result_cliente = mysqli_stmt_get_result($stmt_cliente);

    if (mysqli_num_rows($result_cliente) == 0) {
        $mensaje = "<div class='alert alert-danger'>El cliente seleccionado no existe.</div>";
    }

    $query_especialista = "SELECT especialista_id FROM especialista WHERE especialista_id = ?";
    $stmt_especialista = mysqli_prepare($con, $query_especialista);
    mysqli_stmt_bind_param($stmt_especialista, 'i', $especialista_id);
    mysqli_stmt_execute($stmt_especialista);
    $result_especialista = mysqli_stmt_get_result($stmt_especialista);

    if (mysqli_num_rows($result_especialista) == 0) {
        $mensaje = "<div class='alert alert-danger'>El especialista seleccionado no existe.</div>";
    }

    if (empty($mensaje)) {
        $query_insert_terapia = "
            INSERT INTO terapia (Cliente_cliente_id, Especialista_especialista_id, fecha_inicio, fecha_fin, estado, notas) 
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($con, $query_insert_terapia);
        mysqli_stmt_bind_param($stmt_insert, 'iissss', $cliente_id, $especialista_id, $fecha_inicio, $fecha_fin, $estado, $notas);
        $result_insert = mysqli_stmt_execute($stmt_insert);

        if ($result_insert) {
            $terapia_id = mysqli_insert_id($con);

            if (isset($_SESSION['admin'])) {
                $admin_id = $_SESSION['user_id'];
                $query_bitacora = "
                    INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Admin_admin_id) 
                    VALUES ('Insertar', 'terapia', NOW(), ?, 
                            (SELECT admin_id FROM admin WHERE User_usuario_id = ?))";
                $descripcion = "Nueva terapia ID: $terapia_id, Cliente ID: $cliente_id, Especialista ID: $especialista_id.";
                $stmt_bitacora = mysqli_prepare($con, $query_bitacora);
                mysqli_stmt_bind_param($stmt_bitacora, 'si', $descripcion, $admin_id);
                mysqli_stmt_execute($stmt_bitacora);

                echo "<script>alert('Terapia agregada exitosamente'); window.location.href='terapia.php';</script>";
            } elseif (isset($_SESSION['especialista'])) {
                $especialista_user_id = $_SESSION['user_id'];
                $query_bitacora = "
                    INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Especialista_especialista_id) 
                    VALUES ('Insertar', 'terapia', NOW(), ?, 
                            (SELECT especialista_id FROM especialista WHERE User_usuario_id = ?))";
                $descripcion = "Nueva terapia ID: $terapia_id, Cliente ID: $cliente_id, Especialista ID: $especialista_id.";
                $stmt_bitacora = mysqli_prepare($con, $query_bitacora);
                mysqli_stmt_bind_param($stmt_bitacora, 'si', $descripcion, $especialista_user_id);
                mysqli_stmt_execute($stmt_bitacora);

                echo "<script>alert('Terapia agregada exitosamente'); window.location.href='verClientes.php';</script>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al agregar la terapia: " . mysqli_error($con) . "</div>";
        }
    }
}

$query_clientes = "SELECT c.cliente_id, u.nombre, u.apellido FROM user u INNER JOIN cliente c ON u.usuario_id = c.User_usuario_id";
$result_clientes = mysqli_query($con, $query_clientes);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Terapia</title>
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
            <div class="card-header">Agregar Terapia</div>
            <div class="card-body">
                <?php if ($mensaje)
                    echo $mensaje; ?>
                <form method="post">
                    <div class="form-group">
                        <label for="cliente_id">Cliente:</label>
                        <select class="form-control" id="cliente_id" name="cliente_id" required>
                            <option value="">Seleccione un cliente</option>
                            <?php while ($row_cliente = mysqli_fetch_assoc($result_clientes)): ?>
                                <option value="<?php echo $row_cliente['cliente_id']; ?>">
                                    <?php echo "{$row_cliente['nombre']} {$row_cliente['apellido']}"; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="especialista_id">Especialista:</label>
                        <select class="form-control" id="especialista_id" name="especialista_id" required>
                            <?php
                            if (isset($_SESSION['especialista'])) {
                                $correo_especialista = $_SESSION['especialista'];
                                $query_especialista_actual = "
                                    SELECT e.especialista_id, u.nombre, u.apellido
                                    FROM especialista e
                                    INNER JOIN user u ON e.User_usuario_id = u.usuario_id
                                    WHERE u.correo = ?";
                                $stmt = mysqli_prepare($con, $query_especialista_actual);
                                mysqli_stmt_bind_param($stmt, 's', $correo_especialista);
                                mysqli_stmt_execute($stmt);
                                $result = mysqli_stmt_get_result($stmt);

                                if ($row = mysqli_fetch_assoc($result)) {
                                    echo "<option value=\"{$row['especialista_id']}\" selected>";
                                    echo "{$row['nombre']} {$row['apellido']}</option>";
                                } else {
                                    echo '<option value="">Especialista no encontrado</option>';
                                }
                            } elseif (isset($_SESSION['admin'])) {
                                $query_especialistas = "
                                    SELECT e.especialista_id, u.nombre, u.apellido
                                    FROM especialista e
                                    INNER JOIN user u ON e.User_usuario_id = u.usuario_id";
                                $result_especialistas = mysqli_query($con, $query_especialistas);

                                echo '<option value="">Seleccione un especialista</option>';
                                while ($row = mysqli_fetch_assoc($result_especialistas)) {
                                    echo "<option value=\"{$row['especialista_id']}\">";
                                    echo "{$row['nombre']} {$row['apellido']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="fecha_inicio">Fecha de Inicio:</label>
                        <input type="datetime-local" class="form-control" id="fecha_inicio" name="fecha_inicio"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="fecha_fin">Fecha de Fin:</label>
                        <input type="datetime-local" class="form-control" id="fecha_fin" name="fecha_fin" required>
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado:</label>
                        <select class="form-control" id="estado" name="estado" required>
                            <option value="activo">Activo</option>
                            <option value="finalizado">Finalizado</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="notas">Notas:</label>
                        <textarea class="form-control" id="notas" name="notas" rows="4"></textarea>
                    </div>
                    <div class="form-buttons d-flex justify-content-center mt-3">
                        <!-- Botón Regresar -->
                        <a href="<?php echo isset($_SESSION['admin']) ? 'terapia.php' : 'verClientes.php'; ?>"
                            class="btn btn-regresar mr-2">
                            <img src="volver.png" alt="">
                        </a>

                        <!-- Botón Guardar -->
                        <button type="submit" class="btn btn-primary ml-2">Guardar</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</body>

</html>