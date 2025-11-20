<?php

use model\repositorio\AnuncioRepositorio;
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../../model/repositorio/AnuncioRepositorio.php";
require_once __DIR__ . "/../../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../../model/servico/anuncio/Anuncio.php";
require_once __DIR__ . "/../../../controller/conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new AnuncioRepositorio($pdo);
$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);

$erro = $_GET['erro'] ?? null;

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
    <title>Cadastrar Anúncio - SchweizerPine Shopping</title>
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
            <h2>Cadastro de Anúncio</h2>
            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_anuncio.php" method="post" enctype="multipart/form-data">
                <label for="nomeAnuncio">Nome do Anúncio</label>
                <input type="text" id="nomeAnuncio" name="nome" placeholder="Digite o nome do anúncio" required>

                <label for="cnpj">Categoria do anúncio</label>
                <input type="text" id="categoria" name="categoria" placeholder="Digite a categoria" required>

                <label for="formato">Formato do anúncio</label>
                <select id="formato" name="formato" required>
                    <option value="" disabled selected>Selecione o formato</option>
                    <option value="Quadrado">Quadrado</option>
                    <option value="Horizontal">Horizontal</option>
                    <option value="Noticia_completa">Notícia completa</option>
                    <option value="Carrossel">Carrossel (Menu inicial)</option>
                </select>

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" placeholder="Descreva sua loja..." rows="4"></textarea>

                <label for="imagem">Imagem</label>
                <input type="file" id="imagem" name="imagem" accept="image/*">

                <input type="hidden" id="id" name="id" value="0"/>
                <input type="submit" value="Cadastrar"/>
            </form>
        </div>
    </section>
</main>

</body>

</html>
