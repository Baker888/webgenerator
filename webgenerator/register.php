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
    $repeatPassword = $_POST['repeatPassword'];

    if (!empty($email) && !empty($password) && !empty($repeatPassword)) {
        if ($password == $repeatPassword) {
            $query = "SELECT idUsuario FROM usuarios WHERE email = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 0) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $insert_query = "INSERT INTO usuarios (email, password, fechaRegistro) VALUES (?, ?, NOW())";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("ss", $email, $hashed_password);
                $insert_stmt->execute();
                $insert_stmt->close();

                header("Location: login.php");
                exit();
            } else {
                $error_message = "El correo electrónico ya está registrado";
            }
        } else {
            $error_message = "Las contraseñas no coinciden";
        }
    } else {
        $error_message = "Por favor, completa todos los campos";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarte es simple.</title>
</head>
<body>
    <h2>Registrarte es simple.</h2>

    <?php
    if (isset($error_message)) {
        echo "<p style='color: red;'>$error_message</p>";
    }
    ?>

    <form method="post" action="">
        <label for="email">Correo electrónico:</label>
        <input type="email" name="email" required>

        <br>

        <label for="password">Contraseña:</label>
        <input type="password" name="password" required>

        <br>

        <label for="repeatPassword">Repetir Contraseña:</label>
        <input type="password" name="repeatPassword" required>

        <br>

        <input type="submit" value="Registrarse">
    </form>

    <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
</body>
</html>