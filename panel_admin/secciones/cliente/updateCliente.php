<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: ../../index.php');
    exit();
}
include ('../../conexion.php');

$id = $_GET['id'];
$mensaje = '';

// Obtener datos del cliente y usuario asociados
$query_cliente = "
    SELECT u.nombre, u.apellido, u.correo, u.contrasena, 
           c.direccion, c.telefono, c.Preferencia_animal, c.diagnostico 
    FROM user u 
    INNER JOIN cliente c ON u.usuario_id = c.User_usuario_id 
    WHERE u.usuario_id = $id";
$result_cliente = mysqli_query($con, $query_cliente);
if (!$result_cliente) {
    die("Error en la consulta del cliente: " . mysqli_error($con));
}

$cliente = mysqli_fetch_assoc($result_cliente);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = $_POST['password'] ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $cliente['contrasena']; // Si la contraseña está vacía, usamos la actual
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $preferencia_animal = $_POST['preferencia_animal'];
    $diagnostico = $_POST['diagnostico'];

    // Actualizar en la tabla `user`
    $query_update_user = "
        UPDATE user SET nombre='$nombre', apellido='$apellido', correo='$correo', contrasena='$password' 
        WHERE usuario_id=$id";
    $result_update_user = mysqli_query($con, $query_update_user);

    if ($result_update_user) {
        // Actualizar en la tabla `cliente`
        $query_update_cliente = "
            UPDATE cliente SET direccion='$direccion', telefono='$telefono', 
            Preferencia_animal='$preferencia_animal', diagnostico='$diagnostico' 
            WHERE User_usuario_id=$id";
        $result_update_cliente = mysqli_query($con, $query_update_cliente);

        if ($result_update_cliente) {
            // Registrar en la bitácora
            $admin_user_id = $_SESSION['user_id']; // ID del administrador actual
            $descripcion = "Se actualizó el cliente con ID: $id. Nombre: $nombre $apellido, Correo: $correo, Dirección: $direccion, Teléfono: $telefono, Preferencia Animal: $preferencia_animal, Diagnóstico: $diagnostico.";
            $query_insert_bitacora = "INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Admin_admin_id) 
                                      VALUES ('Actualizar', 'cliente', NOW(), '$descripcion', 
                                      (SELECT admin_id FROM admin WHERE User_usuario_id = $admin_user_id))";
            mysqli_query($con, $query_insert_bitacora);

            echo "<script>alert('Cliente actualizado exitosamente'); window.location.href='cliente.php';</script>";
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al actualizar el cliente: " . mysqli_error($con) . "</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al actualizar el usuario: " . mysqli_error($con) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
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
            border: 2px solid ;
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
            <img src="logoat.jpg" alt="Logo"> <!-- Cambia "logoat.jpg" por la ruta de tu logo -->
        </a>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">Editar Cliente</div>
            <div class="card-body">
                <?php if ($mensaje) echo $mensaje; ?>
                <form method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $cliente['nombre']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido:</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" value="<?php echo $cliente['apellido']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo:</label>
                        <input type="email" class="form-control" id="correo" name="correo" value="<?php echo $cliente['correo']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">👁️</button>
                            </div>
                        </div>
                        <small class="form-text text-muted">Deja el campo en blanco si no deseas cambiar la contraseña.</small>
                    </div>

                    <div class="form-group">
                        <label for="direccion">Dirección:</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo $cliente['direccion']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo $cliente['telefono']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="preferencia_animal">Preferencia Animal:</label>
                        <select class="form-control" id="preferencia_animal" name="preferencia_animal">
                            <option value="">-- Seleccionar --</option>
                            <option value="perro" <?php if ($cliente['Preferencia_animal'] == 'perro') echo 'selected'; ?>>Perro</option>
                            <option value="gato" <?php if ($cliente['Preferencia_animal'] == 'gato') echo 'selected'; ?>>Gato</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="diagnostico">Diagnóstico:</label>
                        <select class="form-control" id="diagnostico" name="diagnostico" required>
                            <option value="estrés" <?php if ($cliente['diagnostico'] == 'estrés') echo 'selected'; ?>>Estrés</option>
                            <option value="ansiedad" <?php if ($cliente['diagnostico'] == 'ansiedad') echo 'selected'; ?>>Ansiedad</option>
                            <option value="depresión" <?php if ($cliente['diagnostico'] == 'depresión') echo 'selected'; ?>>Depresión</option>
                        </select>
                    </div>

                    <div class="form-buttons">
                        <a href="cliente.php" class="btn-regresar">
                            <img src="volver.png" alt=""> 
                        </a>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function togglePassword() {
            const passwordField = document.getElementById('password');
            const button = passwordField.nextElementSibling.firstElementChild;
            if (passwordField.type === "password") {
                passwordField.type = "text";
                button.innerText = "🙈";
            } else {
                passwordField.type = "password";
                button.innerText = "👁️";
            }
        }
    </script>
</body>
</html>
