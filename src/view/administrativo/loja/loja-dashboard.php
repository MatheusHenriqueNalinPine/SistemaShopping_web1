<?php

use model\repositorio\LojaRepositorio;
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../../model/repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../../../model/repositorio/LojaRepositorio.php';
require_once __DIR__ . '/../../../model/repositorio/CategoriaLojaRepositorio.php';

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

require_once __DIR__ . '/../../../controller/conexao-bd.php';

$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);
$repositorio = new LojaRepositorio($pdo);
$categorias = $repositorio->buscarLojas();

$cargo = $usuario->getCargo();
if($cargo == Cargo::Funcionario_cinema || $cargo == Cargo::Gerenciador_anuncio){
    header('Location: /SistemaShopping_web1/src/view/administrativo/administrativo.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <title>Gerenciar Lojas - Administrativo</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/crud-tabela.css">
</head>

<body>

<?php include('../menu.php') ?>
<?php include('../sidebar.php') ?>

<main class="conteudo">
    <h2>Gerenciamento de Lojas</h2>

    <div class="acoes">
        <a href="cadastrar-loja.php" class="btn-cadastrar">Cadastrar loja</a>
        <form action="/SistemaShopping_web1/src/controller/relatorios/gerar-pdf.php" method="get">
            <input type="hidden" name="tipo" id="tipo" value="lojas">
            <input type="submit" class="btn-relatorio" value="Baixar Relatório">
        </form>
    </div>

    <table class="tabela">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Categoria</th>
            <th>Descrição</th>
            <th>Localização</th>
            <th>Remover</th>
            <th>Editar</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($categorias) == 0) : ?>
        <tr>
            <td colspan="7" class="sem-dados">Nenhuma loja cadastrada</td>
        </tr>
        <?php else: ?>
        <?php foreach ($categorias as $loja) : ?>
        <tr>
            <td><?php echo htmlspecialchars($loja->getNome()) ?></td>
            <td><?php echo htmlspecialchars((new \model\repositorio\CategoriaLojaRepositorio($pdo))->buscarPorId($loja->getCategoria()))?></td>
            <td><?php echo htmlspecialchars($loja->getDescricao()) ?></td>
            <td><?php echo htmlspecialchars($loja->getPosicao()) ?></td>
            <td>
                <form action="/SistemaShopping_web1/src/controller/exclusao/excluir-loja.php" method="post">
                    <input type="hidden" name="id" value="<?= $loja->getId() ?>">
                    <input type="submit" class="botao-excluir" value="Excluir">
                </form>
            </td>
            <td><form action="editar-loja.php" method="get">
                    <input type="hidden" name="id" value="<?= $loja->getId() ?>">
                    <input type="submit" class="btn-editar" value="Editar">
                </form></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</body>

</html>