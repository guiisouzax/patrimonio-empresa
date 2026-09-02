<?php

session_start();
require_once "Database.php";

$email = $_POST['email'] ?? '';
$senha = $_POST['senha'] ?? '';


if (empty($email) || empty($senha)) {
    header('Location: index.php?erro=campos_vazios');
    exit;
}

$db = new Database();
$pdo = $db->getConnection();

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$usuario = $stmt->fetch();

if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome'];
    $_SESSION['usuario_tipo'] = $usuario['tipo'];

    header('Location: inventario.php');
    exit;

} else {
    header('Location: index.php?erro=credenciais_invalidas');
    exit;
}