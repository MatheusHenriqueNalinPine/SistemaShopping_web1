<?php

use model\repositorio\CinemaRepositorio;
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../../model/repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../../../model/repositorio/CinemaRepositorio.php';
require_once __DIR__ . '/../../../controller/conexao-bd.php';

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;
if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);
$cargo = $usuario->getCargo();
if ($cargo == Cargo::Funcionario_cinema || $cargo == Cargo::Gerenciador_anuncio) {
    header('Location: /SistemaShopping_web1/src/view/administrativo/administrativo.php');
    exit;
}

$repositorio = new CinemaRepositorio($pdo);
$filmes = $repositorio->buscarTodos();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Filmes - Administrativo</title>
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/crud-tabela.css">
</head>
<body>
<?php include('../menu.php') ?>
<?php include('../sidebar.php') ?>

<main class="conteudo">
    <h2>Gerenciamento de Filmes</h2>

    <div class="acoes">
        <a href="cadastrar-filme.php" class="btn-cadastrar">Cadastrar filme</a>
        <form action="/SistemaShopping_web1/src/controller/relatorios/gerar-pdf.php" method="get">
            <input type="hidden" name="tipo" id="tipo" value="filmes">
            <input type="submit" class="btn-relatorio" value="Baixar Relatório">
        </form>
    </div>
    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'exclusao'): ?>
        <p class="mensagem-erro">Erro ao excluir <?=$_GET['id']?>.</p>
    <?php endif; ?>
    <table class="tabela">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Gênero</th>
            <th>Remover</th>
            <th>Editar</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($filmes) == 0) : ?>
            <tr>
                <td colspan="6" class="sem-dados">Nenhum filme cadastrado</td>
            </tr>
        <?php else: ?>
            <?php foreach ($filmes as $f): ?>
                <tr>
                    <td><?php echo htmlspecialchars($f->getNome()) ?></td>
                    <td><?php echo htmlspecialchars($f->getDescricao()) ?></td>
                    <td><?php echo htmlspecialchars($f->getGenero()) ?></td>
                    <td>
                        <form action="/SistemaShopping_web1/src/controller/exclusao/excluir-filme.php" method="post">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($f->getId()) ?>">
                            <input type="submit" class="botao-excluir" value="Excluir">
                        </form>
                    </td>
                    <td>
                        <form action="editar-filme.php" method="get">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($f->getId()) ?>">
                            <input type="submit" class="btn-editar" value="Editar">
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
