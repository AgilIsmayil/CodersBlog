<?php

require_once '../parts/index.php';

$delete = sqlConnetion()->prepare('DELETE FROM categories WHERE id = ?');

$delete->execute([
    $_GET['id']
]);

header('location: home.php');
