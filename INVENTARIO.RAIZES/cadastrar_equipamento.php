<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

require_once 'Database.php';

$patrimonio = $_POST['patrimonio'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$setor = $_POST['setor'] ?? '';
$responsavel = $_POST['responsavel'] ?? null;
$status = $_POST['status'] ?? '';
$descricao = $_POST['descricao'] ?? null;

if (empty($patrimonio) || empty($tipo) || empty($setor) || empty($status)) {
    header('Location: inventario.php?erro=campos_obrigatorio');
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

try {
    $stmt = $pdo->prepare("INSERT INTO equipamentos (patrimonio, tipo, descricao, setor, responsavel, status)
                            VALUES (:patrimonio, :tipo, :descricao, :setor, :responsavel, :status)");

    $stmt->bindParam(':patrimonio', $patrimonio);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':setor', $setor);
    $stmt->bindParam(':responsavel', $responsavel);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':descricao', $descricao);

    $stmt->execute();

    header('Location: inventario.php?sucesso=1');
    exit;

} catch (PDOException $e) {
    header('Location: inventario.php?erro=patrimonio_duplicado');
    exit;
}