<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../../model/repositorio/UsuarioRepositorio.php';

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

require_once __DIR__ . '/../../../controller/conexao-bd.php';

$repositorio = new UsuarioRepositorio($pdo);
$usuario = ($repositorio->buscarPorEmail($usuario_logado));
$usuarios = $repositorio->buscarTodos();

?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Funcionários - Administrativo</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/crud-tabela.css">
</head>

<body>

<?php include('../menu.php') ?>
<?php include('../sidebar.php') ?>

<main class="conteudo">
    <h2>Gerenciamento de Funcionários</h2>

    <div class="acoes">
        <a href="../../sessoes/cadastrar.php" class="btn-cadastrar">Cadastrar funcionário</a>
        <button class="btn-relatorio">Baixar relatório</button>
    </div>
    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'exclusao'): ?>
        <p class="mensagem-erro">Você não pode excluir a si mesmo.</p>
    <?php endif; ?>
    <table class="tabela">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Cargo</th>
            <th>CPF</th>
            <th>E-mail</th>
            <th>Remover</th>
            <th>Editar</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($usuarios) == 0) : ?>
            <tr>
                <td colspan="5" class="sem-dados">Nenhum usuário cadastrado</td>
            </tr>
        <?php else: ?>
            <?php foreach ($usuarios as $tabela_usuario) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($tabela_usuario->getNome()) ?></td>
                    <td><?php echo htmlspecialchars($tabela_usuario->getCargo()->value) ?></td>
                    <td><?php echo htmlspecialchars($tabela_usuario->getCPF()) ?></td>
                    <td><?php echo htmlspecialchars($tabela_usuario->getEmail()) ?></td>
                    <td>
                        <form action="/SistemaShopping_web1/src/controller/exclusao/excluir-usuario.php" method="post">
                            <input type="hidden" name="id" value="<?= $tabela_usuario->getId() ?>">
                            <input type="submit" class="botao-excluir" value="Excluir">
                        </form>
                    </td>
                    <td>
                        <form action="editar-usuario.php" method="get">
                            <input type="hidden" name="id" value="<?= $tabela_usuario->getId() ?>">
                            <input type="submit" class="btn-cadastrar" value="Editar">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</body>

</html>
