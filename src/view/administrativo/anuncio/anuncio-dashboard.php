<?php

use model\repositorio\AnuncioRepositorio;
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../../model/repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../../../model/repositorio/AnuncioRepositorio.php';

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

require_once __DIR__ . '/../../../controller/conexao-bd.php';

$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);
$repositorio = new AnuncioRepositorio($pdo);
$anuncios = $repositorio->buscarTodos();

$cargo = $usuario->getCargo();
if($cargo == Cargo::Funcionario_cinema || $cargo == Cargo::Lojista){
    header('Location: /SistemaShopping_web1/src/view/administrativo/administrativo.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Anúncios - Administrativo</title>
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/crud-tabela.css">
</head>

<body>

<?php include('../menu.php') ?>
<?php include('../sidebar.php') ?>

<main class="conteudo">
    <h2>Gerenciamento de Anúncios</h2>

    <div class="acoes">
        <a href="cadastrar-anuncio.php" class="btn-cadastrar">Cadastrar anúncio</a>
        <form action="/SistemaShopping_web1/src/controller/relatorios/gerar-pdf.php" method="get">
            <input type="hidden" name="tipo" id="tipo" value="anuncios">
            <input type="submit" class="btn-relatorio" value="Baixar Relatório">
        </form>
    </div>

    <table class="tabela">
        <thead>
        <tr>
            <th>Nome</th>
            <th>Descrição</th>
            <th>Formato</th>
            <th>Remover</th>
            <th>Editar</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($anuncios) == 0) : ?>
        <tr>
            <td colspan="7" class="sem-dados">Nenhum anúncio cadastrado</td>
        </tr>
        <?php else: ?>
        <?php foreach ($anuncios as $anuncio) : ?>
        <tr>
            <td><?php echo htmlspecialchars($anuncio->getNome()) ?></td>
            <td><?php echo htmlspecialchars($anuncio->getDescricao()) ?></td>
            <td><?php echo htmlspecialchars($anuncio->getFormatoAnuncio()->value) ?></td>
            <td>
                <form action="/SistemaShopping_web1/src/controller/exclusao/excluir-anuncio.php" method="post">
                    <input type="hidden" name="id" value="<?= $anuncio->getId() ?>">
                    <input type="submit" class="botao-excluir" value="Excluir">
                </form>
            </td>
            <td><form action="editar-anuncio.php" method="get">
                    <input type="hidden" name="id" value="<?= $anuncio->getId() ?>">
                    <input type="submit" class="btn-cadastrar" value="Editar">
                </form></td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</body>

</html>