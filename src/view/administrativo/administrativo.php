<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../model/repositorio/UsuarioRepositorio.php';

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

require_once __DIR__ . '/../../controller/conexao-bd.php';

$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);
$cargo = $usuario->getCargo();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <title>Painel Administrativo - SchweizerPine</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/administrativo.css">
</head>

<body>
<?php include('sidebar.php') ?>
<?php include('menu.php') ?>

<main class="conteudo">
    <h2>Gerenciadores</h2>
    <div class="cards-container">
        <?php if ($cargo == Cargo::Administrador || $cargo == Cargo::Lojista): ?>
            <a href="loja/loja-dashboard.php" class="card">
                <div class="icon">🛒</div>
                <span>Lojas/Restaurantes</span>
            </a>
            <a href="loja/categoria/categoria-loja-dashboard.php" class="card">
                <div class="icon">🛒</div>
                <span>Categoria lojas</span>
            </a>
        <?php endif;
        if ($cargo == Cargo::Administrador || $cargo == Cargo::Gerenciador_anuncio): ?>
            <a href="/SistemaShopping_web1/src/view/administrativo/anuncio/anuncio-dashboard.php" class="card">
                <div class="icon">📢</div>
                <span>Anúncios</span>
            </a>
            <a href="anuncio/categoria/categoria-anuncio-dashboard.php" class="card">
                <div class="icon">📢</div>
                <span>Categoria anúncios</span>
            </a>
        <?php endif;
        if ($cargo == Cargo::Administrador || $cargo == Cargo::Funcionario_cinema): ?>
            <a href="#" class="card">
                <div class="icon">🎬</div>
                <span>Cinema</span>
            </a>
        <?php endif; ?>
        <a href="usuario/usuarios-dashboard.php" class="card">
            <div class="icon">👥</div>
            <span>Funcionários</span>
        </a>
    </div>
</main>

</body>

</html>
