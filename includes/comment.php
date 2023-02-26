<?php
$stmt = $pdo->query('SELECT COUNT(*) FROM commentaires');
$nombre_total_commentaires = $stmt->fetchColumn();
?>
