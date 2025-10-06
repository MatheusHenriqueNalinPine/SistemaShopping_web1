<?php
session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if(!$usuario_logado) {
    header('Location: login.php?erro=deslogado');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Lojas - Administrativo</title>
    <link rel="stylesheet" href="../../css/loja.css">
</head>

<body>
    <header class="topbar">
        <div class="logo-header">
            <img src="../../img/logoShopping.png" alt="Logo Shopping">
        </div>
        <h1>Administrativo</h1>
        <div class="usuario-info">
            <span>Nome do administrador</span>
        </div>
    </header>

    <aside class="sidebar">
        <ul>
            <a href="administrativo.php">Administrativo</a>
            <a href="./loja-dashboard.php" class="ativo">Lojas</a>
            <a href="#">Anúncios</a>
            <a href="#">Cinema</a>
            <a href="#">Funcionários</a>
            <a href="../controller/autenticacao/logout.php">Sair</a>
        </ul>
    </aside>

    <main class="conteudo">
        <h2>Gerenciamento de Lojas</h2>

        <div class="acoes">
            <a href="cadastrar-loja.php" class="btn-cadastrar">Cadastrar loja</a>
            <button class="btn-relatorio">Baixar relatório</button>
        </div>

        <table class="tabela-lojas">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th>Localização</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="sem-dados">Nenhuma loja cadastrada</td>
                </tr>
            </tbody>
        </table>
    </main>
</body>

</html>
