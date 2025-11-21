<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../model/repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../../controller/conexao-bd.php';

$usuario_logado = $_SESSION['usuario'] ?? null;
$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);
$cargo = $usuario->getCargo();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/crud-tabela.css">
</head>

<aside class="sidebar">
    <ul>
        <a href="/SistemaShopping_web1/src/view/administrativo/administrativo.php">Administrativo</a>
        <?php if ($cargo == Cargo::Administrador || $cargo == Cargo::Lojista): ?>
            <a href="/SistemaShopping_web1/src/view/administrativo/loja/loja-dashboard.php">Lojas</a>
            <a href="/SistemaShopping_web1/src/view/administrativo/loja/categoria/categoria-loja-dashboard.php">Categoria Lojas</a>
        <?php endif;
        if ($cargo == Cargo::Administrador || $cargo == Cargo::Gerenciador_anuncio): ?>
            <a href="/SistemaShopping_web1/src/view/administrativo/anuncio/anuncio-dashboard.php">Anúncios</a>
            <a href="/SistemaShopping_web1/src/view/administrativo/anuncio/categoria/categoria-anuncio-dashboard.php">Categoria Anúncios</a>
        <?php endif;
        if ($cargo == Cargo::Administrador || $cargo == Cargo::Funcionario_cinema): ?>
            <a href="#">Cinema</a>
        <?php endif; ?>
        <a href="/SistemaShopping_web1/src/view/administrativo/usuario/usuarios-dashboard.php">Funcionários</a>
        <a href="/SistemaShopping_web1/src/controller/autenticacao/logout.php">Sair</a>
    </ul>
</aside>

</html>
