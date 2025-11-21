<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../../../controller/conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new UsuarioRepositorio($pdo);
$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);

$erro = $_GET['erro'] ?? null;

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
    <title>Cadastrar Categoria de Anuncio - SchweizerPine Shopping</title>
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/cadastrar.css">
</head>

<body>
<?php include('../../menu.php') ?>
<main>
    <section class="cadastro-container">
        <div class="form-box">
            <?php if (isset($_GET['erro']) && $_GET['erro'] === 'campos-vazios'): ?>
                <p class="mensagem-erro">Não deixe os campos vazios.</p>
            <?php endif; ?>
            <h2>Cadastro de Categoria (Anuncio)</h2>
            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_categoria_anuncio.php" method="post" enctype="multipart/form-data">
                <label for="nomeCategoria">Categoria</label>
                <input type="text" id="nomeCategoria" name="categoria" placeholder="Digite o nome da categoria" required>
                <input type="hidden" id="id" name="id" value="0"/>
                <input class="btn-cadastrar" type="submit" value="Cadastrar"/>
            </form>
        </div>
    </section>
</main>

</body>

</html>
