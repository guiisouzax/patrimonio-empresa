<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'Database.php';

$id = $_POST['id'] ?? '';

if (empty($id)) {
    header('Location: inventario.php?erro=id_invalido');
    exit;
}

$db = new Database(); 
$pdo = $db->getConnection();

$stmt = $pdo->prepare("DELETE FROM equipamentos WHERE id = :id");
$stmt->bindParam(':id', $id);
$stmt->execute();

header('Location: inventario.php?sucesso=excluido');
exit;

