<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'Database.php';

$id = $_POST['id'] ?? '';
$patrimonio = $_POST['patrimonio'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$descricao = $_POST['descricao'] ?? null;
$setor = $_POST['setor'] ?? '';
$responsavel = $_POST['responsavel'] ?? null;
$status = $_POST['status'] ?? '';

if (empty($id) || empty($patrimonio) || empty($tipo) || empty($setor) || empty($status)) {
    header('Location: inventario.php?erro=campos_obrigatorios');
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

try {
    $stmt = $pdo->prepare("UPDATE equipamentos 
                            SET patrimonio = :patrimonio, tipo = :tipo, descricao = :descricao, 
                                setor = :setor, responsavel = :responsavel, status = :status
                            WHERE id = :id");

    $stmt->bindParam(':patrimonio', $patrimonio);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':descricao', $descricao);
    $stmt->bindParam(':setor', $setor);
    $stmt->bindParam(':responsavel', $responsavel);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $id);

    $stmt->execute();

    header('Location: inventario.php?sucesso=editado');
    exit;

} catch (PDOException $e) {
    header('Location: inventario.php?erro=patrimonio_duplicado');
    exit;
}