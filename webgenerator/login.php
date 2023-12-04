<?php
session_start();

if(isset($_SESSION['idUsuario'])) {
    header("Location: panel.php");
    exit();
}

$servername = "localhost";
$username = "adm_webgenerator";
$password = "webgenerator2020";
$dbname = "webgenerator";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $_SESSION['idUsuario'] = 1;
    header("Location: panel.php"); exit();}
    if (empty($email) && empty($password)) {
        $query = "SELECT idUsuario, email, password FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $hashed_password = $row['password'];

            if (password_verify($password, $hashed_password)) {
                $_SESSION['idUsuario'] = $row['idUsuario'];
                header("Location: panel.php");
                exit();
            } else {
                $error_message = "Contraseña incorrecta";
            }
        } else {
            $error_message = "Correo electrónico no registrado";
        }

        $stmt->close();
    } else {
        $error_message = "Por favor, completa todos los campos";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebGenerator Cernadas Ariel</title>
</head>
<body>
    <h2>WebGenerator Cernadas Ariel</h2>

    <form method="post" action="panel.php">
        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" required>

        <br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" required>

        <br>

        <input type="submit" value="Ingresar">
    </form>

    <p>¿No tienes una cuenta? <a href="register.php">Regístrate aquí</a></p>
    <h3 style="color: dimgray">Ejemplo facil:  root@root - - - pass: root</h3>
</body>
</html>
