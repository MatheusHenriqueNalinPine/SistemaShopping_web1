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
    <link rel="stylesheet" href="../../../css/crud.css">
</head>

<header class="topbar">
    <div class="logo-header">
        <img src="../../../img/logoShopping.png" alt="Logo Shopping">
    </div>
    <h1>Administrativo</h1>
    <div class="usuario-info">
        <span><?php echo htmlspecialchars($usuario->getNome()); ?></span>
    </div>
</header>

<aside class="sidebar">
    <ul>
        <a href="administrativo.php">Administrativo</a>
        <a href="loja-dashboard.php">Lojas</a>
        <a href="#">Anúncios</a>
        <a href="#">Cinema</a>
        <a href="usuarios-dashboard.php">Funcionários</a>
        <a href="../../controller/autenticacao/logout.php">Sair</a>
    </ul>
</aside>

</html>
