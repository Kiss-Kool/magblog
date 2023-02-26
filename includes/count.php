<?php
require_once 'config.php';
define('TABLE_ARTICLES', 'articles');
define('CHAMP_ID', 'id');

// Count the number of items in the articles table
$stmt = $pdo->query('SELECT COUNT('.CHAMP_ID.') as count FROM '.TABLE_ARTICLES);
$result = $stmt->fetch(PDO::FETCH_ASSOC);
$count = $result['count'];

?>
