<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../../controller/conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new UsuarioRepositorio($pdo);
$id = $_GET['id'] ?? null;
$usuario = (new UsuarioRepositorio($pdo))->buscarPorId($id);

$erro = $_GET['erro'] ?? null;

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar usuário - SchweizerPine Shopping</title>
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/cadastrar.css">
</head>

<body>

<?php include('../../sessoes/header.html'); ?>

<main>
    <section class="cadastro-container">
        <div class="form-box">
            <?php
            if (isset($_GET['erro']) && $_GET['erro'] === 'campos-vazios'): ?>
                <p class="mensagem-erro">Não deixe os campos vazios.</p>
            <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'cpf-repetido'): ?>
                <p class="mensagem-erro">CPF Repetido, tente novamente.</p>
            <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'email-repetido'): ?>
                <p class="mensagem-erro">E-mail Repetido, tente novamente.</p>
            <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'senhas-diferentes'): ?>
                <p class="mensagem-erro">As senhas não correspondem.</p>
            <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'cpf-invalido'): ?>
                <p class="mensagem-erro">Apenas números (11) no CPF, tente novamente.</p>
            <?php endif; ?>
            <h2>Cadastro</h2>
            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_usuario.php" method="post">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" placeholder="Digite seu nome"
                       value="<?= $usuario->getNome() ?>" required>

                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail"
                       value="<?= $usuario->getEmail() ?>" required>

                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" placeholder="Digite seu CPF (apenas números)"
                       value="<?= $usuario->getCpf() ?>" required>

                <label for="senha">Senha OPCIONAL</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua nova senha (opcional)">

                <label for="confirmar">Confirmar Senha OPCIONAL</label>
                <input type="password" id="confirmar" name="confirmar" placeholder="Repita a senha (opcional)">

                <label for="tipo">Tipo de Funcionário</label>
                <select id="tipo" name="cargo" required>
                    <option value="" disabled>Selecione o tipo</option>
                    <option value="administrador" <?= $usuario->getCargo()->value === 'administrador' ? 'selected' : '' ?>>
                        Administrador Geral
                    </option>
                    <option value="lojista" <?= $usuario->getCargo()->value === 'lojista' ? 'selected' : '' ?>>
                        Lojista
                    </option>
                    <option value="cinema" <?= $usuario->getCargo()->value === 'cinema' ? 'selected' : '' ?>>
                        Cinema
                    </option>
                    <option value="anuncio" <?= $usuario->getCargo()->value === 'anuncio' ? 'selected' : '' ?>>
                        Gerenciador de Anúncios
                    </option>
                </select>


                <input type="hidden" name="id" value="<?=$id?>">

                <button type="submit" class="btn-cadastrar">Salvar alterações</button>
            </form>
        </div>
    </section>
</main>

</body>

</html>