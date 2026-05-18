<?php
require_once 'config/database.php';
session_start();

if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 2) {
    header('Location: index.php');
    exit;
}

$estudiante_id = $_SESSION['user_id'];

$query = "SELECT 
            a.nombre AS asignatura,
            l.nombre AS lugar,
            n.parcial,
            n.teoria,
            n.practica,
            (n.teoria + n.practica)/2 AS promedio
          FROM notas n
          JOIN asignaturas a ON n.asignatura_id = a.id
          JOIN asignaturas_estudiante ae ON ae.asignatura_id = a.id AND ae.usuario_id = n.usuario_id
          JOIN lugares l ON ae.lugar_id = l.id
          WHERE n.usuario_id = ?";

$stmt = $pdo->prepare($query);
$stmt->execute([$estudiante_id]);
$mis_notas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Calificaciones</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <a href="logout.php" class="logout-btn">Cerrar Sesión</a>
        <h1>Portal del Estudiante: <?php echo $_SESSION['nombre']; ?></h1>
        <hr>
        <h2>Mis Notas Registradas</h2>
        <?php if(count($mis_notas) > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Institución</th>
                    <th>Asignatura</th>
                    <th>Periodo / Parcial</th>
                    <th>Nota Teoría</th>
                    <th>Nota Práctica</th>
                    <th>Promedio Final</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($mis_notas as $nota): ?>
                <tr>
                    <td><?php echo $nota['lugar']; ?></td>
                    <td><?php echo $nota['asignatura']; ?></td>
                    <td>
                        <?php 
                            if($nota['parcial'] == 1) echo "1er Parcial";
                            elseif($nota['parcial'] == 2) echo "2do Parcial";
                            else echo "Mejoramiento";
                        ?>
                    </td>
                    <td><?php echo $nota['teoria']; ?></td>
                    <td><?php echo $nota['practica']; ?></td>
                    <td><?php echo number_format($nota['promedio'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <p>Aún no tienes calificaciones cargadas en el sistema.</p>
        <?php endif; ?>
    </div>
</body>
</html>