<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../model/repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../../controller/conexao-bd.php';

$usuario_logado = $_SESSION['usuario'] ?? null;
$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/crud-tabela.css">
</head>

<header class="topbar">
    <div class="logo-header">
        <a href="/SistemaShopping_web1/index.php"><img src="/SistemaShopping_web1/img/logoShopping.png" alt="Logo Shopping"></a>
    </div>
    <h1>Administrativo</h1>
    <div class="usuario-info">
        <span><?php echo htmlspecialchars($usuario->getNome()); ?></span>
    </div>
</header>
</html>