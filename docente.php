<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['guardar_nota'])) {
    $asignatura_id = $_POST['asignatura_id'];
    $estudiante_id = $_POST['usuario_id'];
    $parcial = $_POST['parcial'];
    $teoria = $_POST['teoria'];
    $practica = $_POST['practica'];
    $docente_id = $_SESSION['user_id'];

    $fecha_actual = date('Y-m-d H:i:s');
    $hora_actual = date('H:i:s');

    $stmt = $pdo->prepare("SELECT id FROM notas WHERE asignatura_id = ? AND usuario_id = ? AND parcial = ?");
    $stmt->execute([$asignatura_id, $estudiante_id, $parcial]);
    $nota_existente = $stmt->fetch();

    if ($nota_existente) {
        $sql = "UPDATE notas SET teoria = ?, practica = ?, usuario_id_actualizacion = ?, fecha_actualizacion = ?, hora_actualizacion = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$teoria, $practica, $docente_id, $fecha_actual, $hora_actual, $nota_existente['id']]);
    } else {
        $sql = "INSERT INTO notas (asignatura_id, usuario_id, parcial, teoria, practica, usuario_id_creacion, fecha_creacion, hora_creacion) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$asignatura_id, $estudiante_id, $parcial, $teoria, $practica, $docente_id, $fecha_actual, $hora_actual]);
    }
    header("Location: docente.php?success=1");
    exit;
}

$query = "SELECT 
            ae.usuario_id AS estudiante_id, 
            u.nombre AS estudiante_nombre, 
            u.email AS estudiante_email,
            a.id AS asignatura_id, 
            a.nombre AS asignatura_nombre, 
            l.nombre AS lugar_nombre,
            n.parcial, n.teoria, n.practica
          FROM asignaturas_estudiante ae
          JOIN usuarios u ON ae.usuario_id = u.id
          JOIN asignaturas a ON ae.asignatura_id = a.id
          JOIN lugares l ON ae.lugar_id = l.id
          LEFT JOIN notas n ON n.asignatura_id = a.id AND n.usuario_id = u.id";
$estudiantes = $pdo->query($query)->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Docente</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
        <h1>Panel del Docente: <?php echo $_SESSION['nombre']; ?></h1>
        <hr>
        <h2>Listado de Alumnos y Asignación de Notas</h2>
        <?php if(isset($_GET['success'])): ?>
            <div style="background-color: #2ecc71; color: white; padding: 10px; margin-bottom: 15px;">Nota procesada correctamente.</div>
        <?php endif; ?>
        <table>
            <thead>
                <tr>
                    <th>Estudiante</th>
                    <th>Lugar / Institución</th>
                    <th>Asignatura</th>
                    <th>Parcial</th>
                    <th>Nota Teoría</th>
                    <th>Nota Práctica</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($estudiantes as $row): ?>
                <tr>
                    <form action="" method="POST">
                        <td><?php echo $row['estudiante_nombre']; ?></td>
                        <td><?php echo $row['lugar_nombre']; ?></td>
                        <td><?php echo $row['asignatura_nombre']; ?></td>
                        <td>
                            <select name="parcial" style="width: auto;">
                                <option value="1">1er Parcial</option>
                                <option value="2">2do Parcial</option>
                                <option value="3">Mejoramiento</option>
                            </select>
                        </td>
                        <td><input type="number" name="teoria" step="0.01" min="0" max="10" value="<?php echo $row['teoria'] ?? ''; ?>" required style="width: 80px;"></td>
                        <td><input type="number" name="practica" step="0.01" min="0" max="10" value="<?php echo $row['practica'] ?? ''; ?>" required style="width: 80px;"></td>
                        <td>
                            <input type="hidden" name="asignatura_id" value="<?php echo $row['asignatura_id']; ?>">
                            <input type="hidden" name="usuario_id" value="<?php echo $row['estudiante_id']; ?>">
                            <button type="submit" name="guardar_nota">Guardar</button>
                        </td>
                    </form>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>