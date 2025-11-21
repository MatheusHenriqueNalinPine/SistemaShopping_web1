<?php

use model\repositorio\CategoriaAnuncioRepositorio;
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../../../model/repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../../../../model/repositorio/CategoriaAnuncioRepositorio.php';

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

require_once __DIR__ . '/../../../../controller/conexao-bd.php';

$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);
$repositorio = new CategoriaAnuncioRepositorio($pdo);
$categorias = $repositorio->buscarTodas();

$cargo = $usuario->getCargo();
if ($cargo == Cargo::Funcionario_cinema || $cargo == Cargo::Gerenciador_anuncio) {
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
    <title>Gerenciar Categorias Anuncios - Administrativo</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/crud-tabela.css">
</head>

<body>

<?php include('../../menu.php') ?>
<?php include('../../sidebar.php') ?>

<main class="conteudo">
    <h2>Gerenciamento de Categorias (anuncios)</h2>

    <div class="acoes">
        <a href="cadastrar-categoria.php" class="btn-cadastrar">Cadastrar categoria</a>
    </div>
    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'usada'): ?>
        <p class="mensagem-erro">Essa categoria está sendo usada por: <?php
        foreach ((new CategoriaAnuncioRepositorio($pdo))->whoUse($_GET['erro_id']) as $usado){
            echo htmlspecialchars($usado . "; ");
        }?></p>
    <?php endif; ?>
    <table class="tabela">
        <thead>
        <tr>
            <th>Id</th>
            <th>Categoria</th>
            <th>Remover</th>
            <th>Editar</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($categorias) == 0) : ?>
            <tr>
                <td colspan="7" class="sem-dados">Nenhuma categoria cadastrada</td>
            </tr>
        <?php else: ?>
            <?php foreach ($categorias as $categoria) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($categoria['id']) ?></td>
                    <td><?php echo htmlspecialchars($categoria['categoria']) ?></td>
                    <td>
                        <form action="/SistemaShopping_web1/src/controller/exclusao/excluir-categoria-anuncio.php" method="post">
                            <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
                            <input type="submit" class="botao-excluir" value="Excluir">
                        </form>
                    </td>
                    <td>
                        <form action="editar-categoria.php" method="get">
                            <input type="hidden" name="categoria" value="<?= $categoria['categoria'] ?>">
                            <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
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