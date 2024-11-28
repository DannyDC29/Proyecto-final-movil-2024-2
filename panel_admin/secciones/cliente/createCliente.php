<?php
session_start();
if (!isset($_SESSION['admin']) && !isset($_SESSION['especialista'])) {
    header('Location: ../../index.php');
    exit();
}

include('../../conexion.php');

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $apellido = $_POST['apellido'];
    $correo = $_POST['correo'];
    $password = $_POST['password'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $diagnostico = $_POST['diagnostico'];

    // Encriptar la contraseña antes de almacenarla
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Inserción en la tabla `user`
    $query_insert_user = "INSERT INTO user (nombre, apellido, correo, contrasena) VALUES ('$nombre', '$apellido', '$correo', '$password_hashed')";
    $result_insert_user = mysqli_query($con, $query_insert_user);

    if ($result_insert_user) {
        $usuario_id = mysqli_insert_id($con);

        // Inserción en la tabla `cliente`
        $query_insert_cliente = "INSERT INTO cliente (User_usuario_id, direccion, telefono, diagnostico) VALUES ('$usuario_id', '$direccion', '$telefono', '$diagnostico')";
        $result_insert_cliente = mysqli_query($con, $query_insert_cliente);

        if ($result_insert_cliente) {
            // Registrar en la bitácora para ambos roles
            if (isset($_SESSION['admin'])) {
                $admin_id = $_SESSION['user_id']; // ID del administrador

                // Consultar el ID del administrador
                $query_get_admin_id = "SELECT admin_id FROM admin WHERE User_usuario_id = $admin_id";
                $result_admin_id = mysqli_query($con, $query_get_admin_id);
                $admin_data = mysqli_fetch_assoc($result_admin_id);
                $admin_db_id = $admin_data['admin_id'] ?? null; // Usar null si no se encuentra el admin_id

                $descripcion = "El Admin con ID: $admin_id agregó un nuevo cliente con ID: $usuario_id, Nombre: $nombre $apellido, Correo: $correo, Dirección: $direccion, Teléfono: $telefono";

                $query_insert_bitacora = "INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Admin_admin_id) 
                                          VALUES ('Insertar', 'cliente', NOW(), '$descripcion', " . ($admin_db_id ? $admin_db_id : "NULL") . ")";
                mysqli_query($con, $query_insert_bitacora);
            } elseif (isset($_SESSION['especialista'])) {
                $especialista_id = $_SESSION['user_id']; // ID del especialista

                // Consultar el ID del especialista
                $query_get_especialista_id = "SELECT especialista_id FROM especialista WHERE User_usuario_id = $especialista_id";
                $result_especialista_id = mysqli_query($con, $query_get_especialista_id);
                $especialista_data = mysqli_fetch_assoc($result_especialista_id);
                $especialista_db_id = $especialista_data['especialista_id'] ?? null; // Usar null si no se encuentra el especialista_id

                $descripcion = "El Especialista con ID: $especialista_id agregó un nuevo cliente con ID: $usuario_id, Nombre: $nombre $apellido, Correo: $correo, Dirección: $direccion, Teléfono: $telefono";

                $query_insert_bitacora = "INSERT INTO bitacora (accion, entidad, fecha_hora, descripcion, Especialista_especialista_id) 
                                          VALUES ('Insertar', 'cliente', NOW(), '$descripcion', " . ($especialista_db_id ? $especialista_db_id : "NULL") . ")";
                mysqli_query($con, $query_insert_bitacora);
            }

            // Redirigir al especialista o admin a la página de confirmación
            if (isset($_SESSION['especialista'])) {
                header("Location: confirmacionCliente.php?nombre=$nombre&correo=$correo&password=$password");
                exit();
            } else {
                echo "<script>alert('Cliente agregado exitosamente'); window.location.href='cliente.php';</script>";
            }
        } else {
            $mensaje = "<div class='alert alert-danger'>Error al agregar el cliente: " . mysqli_error($con) . "</div>";
        }
    } else {
        $mensaje = "<div class='alert alert-danger'>Error al agregar el usuario: " . mysqli_error($con) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Cliente</title>
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
            <div class="card-header">Agregar Cliente</div>
            <div class="card-body">
                <?php if ($mensaje)
                    echo $mensaje; ?>
                <form method="post">
                    <div class="form-group">
                        <label for="nombre">Nombre:</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido:</label>
                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                    </div>
                    <div class="form-group">
                        <label for="correo">Correo:</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required>
                            <div class="input-group-append">
                                <button type="button" class="btn btn-outline-secondary"
                                    onclick="togglePassword()">👁️</button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="direccion">Dirección:</label>
                        <input type="text" class="form-control" id="direccion" name="direccion" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono:</label>
                        <input type="text" class="form-control" id="telefono" name="telefono" required>
                    </div>
                    <div class="form-group">
                        <label for="diagnostico">Diagnóstico:</label>
                        <select class="form-control" id="diagnostico" name="diagnostico" required>
                            <option value="">-- Sin asignar --</option>
                            <option value="estrés">Estrés</option>
                            <option value="ansiedad">Ansiedad</option>
                            <option value="depresión">Depresión</option>
                        </select>
                    </div>
                    <div class="form-buttons">
                        <a href="<?php echo (isset($_SESSION['admin'])) ? 'cliente.php' : 'verClientes.php'; ?>"
                            class="btn-regresar">
                            <img src="volver.png" alt="">
                        </a>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>

                </form>
            </div>
        </div>
    </div>

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