<?php
session_start();
if (!isset($_SESSION['especialista'])) {
    header('Location: index.php');
    exit();
}

include('conexion.php');

// Validar la sesión y obtener el correo
if (isset($_SESSION['especialista'])) {
    $correo_especialista = mysqli_real_escape_string($con, $_SESSION['especialista']);
} else {
    // Si la sesión no está configurada, redirigir al inicio de sesión
    header('Location: index.php');
    exit();
}

// Obtener el nombre del especialista basado en el correo almacenado en la sesión
$query_especialista = "SELECT nombre FROM user WHERE correo = '$correo_especialista'";
$result_especialista = mysqli_query($con, $query_especialista);

// Verificar que la consulta devolvió un resultado
if ($result_especialista && mysqli_num_rows($result_especialista) > 0) {
    $especialista = mysqli_fetch_assoc($result_especialista);
    $nombre_especialista = htmlspecialchars($especialista['nombre']); // Escapar salida
} else {
    // Si no se encuentra al especialista, cerrar la sesión y redirigir
    session_destroy();
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Especialista - Animal Therapy</title>
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
            background-color: #1b185c !important;
            color: #fff !important;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }
        .welcome-panel {
            background: #ffffff;
            border-radius: 12px;
            padding: 3rem 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 600px;
        }
        .welcome-panel h1 {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1b185c;
        }
        .welcome-panel p {
            font-size: 1.2rem;
            color: #555;
            margin-top: 1rem;
        }
        .btn-primary {
            background-color: #1b185c;
            border: none;
            padding: 0.75rem 2rem;
            font-size: 1.1rem;
            font-weight: bold;
            border-radius: 8px;
            transition: background-color 0.3s, transform 0.3s;
            margin-top: 1.5rem;
            color: #fff;
        }
        .btn-primary:hover {
            background-color: #1b185c;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
        <a class="navbar-brand" href="#">
            <img src="logoat.jpg" alt="Animal Therapy Logo"> 
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="index2.php">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="secciones/cliente/verClientes.php">Mis registros</a></li>
                <li class="nav-item"><a class="nav-link" href="secciones/cliente/createCliente.php">Registrar paciente</a></li>
                <li class="nav-item"><a class="nav-link" href="secciones/terapia/createTerapia.php">Registrar terapias</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="welcome-panel">
            <h1>Bienvenido, <?php echo $nombre_especialista; ?></h1>
            <p>Gracias por traer nuevos amiguitos a nuestros guardianes.</p>
            <a href="secciones/cliente/createCliente.php" class="btn btn-primary">Empecemos</a>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
