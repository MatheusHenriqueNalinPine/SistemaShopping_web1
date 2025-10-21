<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../model/repositorio/UsuarioRepositorio.php';

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if(!$usuario_logado) {
    header('Location: login.php?erro=deslogado');
    exit;
}

require_once __DIR__ . '/../../controller/conexao-bd.php';

$usuario = (new UsuarioRepositorio($pdo))-> buscarPorEmail($usuario_logado);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - SchweizerPine</title>
    <link rel="stylesheet" href="../../../css/administrativo.css">
</head>

<body>
    <?php include ('menu_sidebar.php')?>

    <main class="conteudo">
        <h2>Gerenciadores</h2>

        <div class="cards-container">
            <a href="loja-dashboard.php" class="card">
                <div class="icon">🛒</div>
                <span>Lojas/Restaurantes</span>
            </a>

            <a href="usuarios-dashboard.php" class="card">
                <div class="icon">👥</div>
                <span>Funcionários</span>
            </a>

            <a href="#" class="card">
                <div class="icon">🎬</div>
                <span>Cinema</span>
            </a>

            <a href="#" class="card">
                <div class="icon">📢</div>
                <span>Anúncios</span>
            </a>
        </div>
    </main>

</body>

</html>
