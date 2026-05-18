<?php
require_once 'config/database.php';
session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $contrasena = trim($_POST['contrasena']);

    if (!empty($email) && !empty($contrasena)) {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ? AND contrasena = ?");
        $stmt->execute([$email, $contrasena]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['rol'] = $user['rol'];

            if ($user['rol'] == 1) {
                header('Location: docente.php');
            } else {
                header('Location: estudiante.php');
            }
            exit;
        } else {
            $error = "Credenciales incorrectas.";
        }
    } else {
        $error = "Por favor, llene todos los campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>PWA - Sistema de Calificaciones</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        <?php if(!empty($error)): ?>
            <div class="alert"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" name="contrasena" required>
            </div>
            <button type="submit">Ingresar</button>
        </form>
    </div>
</body>
</html>