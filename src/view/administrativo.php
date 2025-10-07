<?php

require_once __DIR__ . '/../model/repositorio/UsuarioRepositorio.php';

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if(!$usuario_logado) {
    header('Location: login.php?erro=deslogado');
    exit;
}

require_once __DIR__ . '/../controller/conexao-bd.php';

$usuario = (new \model\repositorio\UsuarioRepositorio($pdo))-> buscarPorEmail($usuario_logado);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - SchweizerPine</title>
    <link rel="stylesheet" href="../../css/administrativo.css">
</head>

<body>
    <header class="topbar">
        <div class="logo-header">
            <img src="../../img/logoShopping.png" alt="Logo Shopping">
        </div>
        <h1>Administrativo</h1>
        <div class="usuario-info">
            <span><?php echo htmlspecialchars($usuario->getNome()); ?></span>
        </div>
    </header>

    <aside class="sidebar">
        <ul>
            <a href="../../index.html">Inicio</a>
            <a href="loja-dashboard.php">Lojas</a>
            <a href="#">Anúncios</a>
            <a href="#">Cinema</a>
            <a href="#">Funcionários</a>
            <a href="../controller/autenticacao/logout.php">Sair</a>
        </ul>
    </aside>

    <main class="conteudo">
        <h2>Gerenciadores</h2>

        <div class="cards-container">
            <a href="loja-dashboard.php" class="card">
                <div class="icon">🛒</div>
                <span>Lojas/Restaurantes</span>
            </a>

            <a href="#" class="card">
                <div class="icon">👥</div>
                <span>Funcionários</span>
            </a>

            <a href="#" class="card">
                <div class="icon">🎬</div>
                <span>Cinema</span>
            </a>

            <a href="#" class="card">
                <div class="icon">👤</div>
                <span>Clientes</span>
            </a>

            <a href="#" class="card">
                <div class="icon">📢</div>
                <span>Anúncios</span>
            </a>
        </div>
    </main>

</body>

</html>
