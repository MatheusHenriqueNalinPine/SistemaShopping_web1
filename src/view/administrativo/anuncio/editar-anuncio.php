<?php

use model\repositorio\AnuncioRepositorio;
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../../model/repositorio/AnuncioRepositorio.php";
require_once __DIR__ . "/../../../model/servico/anuncio/Anuncio.php";
require_once __DIR__ . "/../../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../../controller/conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new AnuncioRepositorio($pdo);
$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);

$idAnuncio = $_GET['id'] ?? null;
$anuncio = $repositorio->buscarPorId($idAnuncio);

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
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/logoShopping.png">
    <title>Editar Anúncio - SchweizerPine Shopping</title>
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
            <h2>Editar Anúncio</h2>
            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_anuncio.php" method="post" enctype="multipart/form-data">
                <label for="nomeAnuncio">Nome do Anúncio</label>
                <input type="text" id="nomeAnuncio" name="nome" placeholder="Digite o nome do anúncio" value="<?=$anuncio->getNome()?>" required>

                <label for="cnpj">Categoria do anúncio</label>
                <input type="text" id="categoria" name="categoria" placeholder="Digite a categoria" required>

                <label for="formato">Formato do anúncio</label>
                <select id="formato" name="formato" required>
                    <option value="" disabled selected>Selecione o formato</option>
                    <option value="<?= \model\servico\anuncio\FormatoAnuncio::Quadrado->value ?>" <?= $anuncio->getFormatoAnuncio()->value === \model\servico\anuncio\FormatoAnuncio::Quadrado->value ? 'selected' : '' ?>>Quadrado</option>
                    <option value="<?= \model\servico\anuncio\FormatoAnuncio::Horizontal->value ?>" <?= $anuncio->getFormatoAnuncio()->value === \model\servico\anuncio\FormatoAnuncio::Horizontal->value ? 'selected' : '' ?>>Horizontal</option>
                    <option value="<?= \model\servico\anuncio\FormatoAnuncio::NoticiaCompleta->value ?>" <?= $anuncio->getFormatoAnuncio()->value === \model\servico\anuncio\FormatoAnuncio::NoticiaCompleta->value ? 'selected' : '' ?>>Notícia completa</option>
                    <option value="<?= \model\servico\anuncio\FormatoAnuncio::Carrossel->value ?>" <?= $anuncio->getFormatoAnuncio()->value === \model\servico\anuncio\FormatoAnuncio::Carrossel->value ? 'selected' : '' ?>>Carrossel (Menu inicial)</option>
                </select>

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" placeholder="Descreva seu anúncio..." rows="4"><?=$anuncio->getDescricao()?></textarea>

                <input type="file" name="imagem" accept="image/*">

                <?php if (!empty($anuncio->getNomeImagem())): ?>
                    <div class="preview-imagem">
                        <p>Imagem atual: <?= htmlspecialchars($anuncio->getNomeImagem()) ?></p>
                        <img src="<?= htmlspecialchars('/SistemaShopping_web1/img/anuncios/' . $anuncio->getNomeImagem()) ?>"
                             alt="Imagem do anúncio" style="max-width:200px;">
                        <input type="hidden" name="imagem_existente"
                               value="<?= htmlspecialchars($anuncio->getNomeImagem()) ?>">
                        <input type="hidden" name="tipo_imagem_existente"
                               value="<?= htmlspecialchars($anuncio->getTipoImagem()) ?>">
                        <input type="hidden" name="url_imagem_existente"
                               value="<?= htmlspecialchars($anuncio->getUrlImagem()) ?>">
                    </div>
                <?php endif; ?>
                <input type="hidden" name="id" value="<?= $anuncio->getId() ?>">

                <input type="submit" value="Editar"/>
            </form>
        </div>
    </section>
</main>

</body>

</html>
