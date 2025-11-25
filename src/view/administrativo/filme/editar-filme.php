<?php

use model\repositorio\CinemaRepositorio;
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../../model/repositorio/CinemaRepositorio.php";
require_once __DIR__ . "/../../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../../controller/conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorioUsuario = new UsuarioRepositorio($pdo);
$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);

$erro = $_GET['erro'] ?? null;

$cargo = $usuario->getCargo();
if ($cargo == Cargo::Lojista || $cargo == Cargo::Gerenciador_anuncio) {
    header('Location: /SistemaShopping_web1/src/view/administrativo/administrativo.php');
    exit;
}

$repositorio = new CinemaRepositorio($pdo);
$id = $_GET['id'] ?? null;
$filme = $repositorio->buscarPorId($id);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Filme - SchweizerPine Shopping</title>
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/cadastrar.css">
</head>

<body>
<?php include('../menu.php') ?>
<main>
    <section class="cadastro-container">
        <div class="form-box">
            <?php if (isset($_GET['erro']) && $_GET['erro'] === 'campos-vazios'): ?>
                <p class="mensagem-erro">Não deixe os campos vazios.</p>
            <?php endif; ?>
            <h2>Edição de Filme</h2>
            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_filme.php" method="post"
                  enctype="multipart/form-data">
                <label for="nomeFilme">Nome do Filme</label>
                <input type="text" id="nomeFilme" name="nome" placeholder="Digite o nome do filme" value="<?=$filme->getNome()?>" required>

                <label for="genero">Gênero do filme</label>
                <input type="text" id="genero" name="genero" placeholder="Ação/Aventura" value="<?=$filme->getGenero()?>" required>

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" placeholder="Descreva seu filme..." rows="4"><?=$filme->getDescricao()?></textarea>

                <label for="imagem">(OPCIONAL) Imagem</label>
                <input type="file" name="imagem" accept="image/*">

                <?php if (!empty($filme->getNomeImagem())): ?>
                    <div class="preview-imagem">
                        <p>Imagem atual: <?= htmlspecialchars($filme->getNomeImagem()) ?></p>
                        <img src="<?= htmlspecialchars('/SistemaShopping_web1/img/filmes/' . $filme->getNomeImagem()) ?>"
                             alt="Imagem do filme" style="max-width:200px;">
                        <input type="hidden" name="imagem_existente"
                               value="<?= htmlspecialchars($filme->getNomeImagem()) ?>">
                        <input type="hidden" name="tipo_imagem_existente"
                               value="<?= htmlspecialchars($filme->getTipoImagem()) ?>">
                        <input type="hidden" name="url_imagem_existente"
                               value="<?= htmlspecialchars($filme->getUrlImagem()) ?>">
                    </div>
                <?php endif; ?>

                <input type="hidden" id="id" name="id" value="<?=$filme->getId()?>"/>
                <input class="btn-cadastrar" type="submit" value="Cadastrar"/>
            </form>
        </div>
    </section>
</main>

</body>

</html>
