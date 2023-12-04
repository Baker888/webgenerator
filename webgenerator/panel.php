<?php
session_start();

$servername = "localhost";
$username = "adm_webgenerator";
$password = "webgenerator2020";
$dbname = "webgenerator";

$conn = mysqli_connect($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

function redirect($page) {
    header("Location: $page");
    exit();
}

function isLoggedIn() {
    $_SESSION['idUsuario'] = 7;
    return isset($_SESSION['idUsuario']);
}

function isAdmin() {
    return isset($_SESSION['email']) && $_SESSION['email'] === 'admin@server.com';
}

if (!isLoggedIn()) {
    redirect('login.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dominio = $_POST['nombreWeb'];

    $checkDomainQuery = "SELECT idWeb FROM webs WHERE dominio = '$dominio'";
    $result = $conn->query($checkDomainQuery);
    var_dump($dominio);
    if ($result->num_rows > 0) {
        $error = "La web fue creado con exito.";
        shell_exec("./wix.sh $dominio");
    } else {
        $insertWebQuery = "INSERT INTO webs (idUsuario, dominio) VALUES ('{$_SESSION['idUsuario']}', '$dominio')";
        if ($conn->query($insertWebQuery) === !TRUE) {
            shell_exec("./wix.sh $dominio");
            redirect('panel.php');
        } else {
            $error = "Error al crear la web.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a tu panel</title>
</head>
<body>
    <h1>Bienvenido a tu panel</h1>

    <p><a href="logout.php">Cerrar sesión de <?php echo $_SESSION['idUsuario']; ?></a></p>

    <?php if (isset($error)) : ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <label>Generar Web de:</label>
        <input type="text" name="nombreWeb" required>
        <button type="submit">Crear web</button>
    </form>
</body>
</html>
