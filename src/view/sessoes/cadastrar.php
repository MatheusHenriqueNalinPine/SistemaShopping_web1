<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../controller/conexao-bd.php";

session_start();

$repositorio = new UsuarioRepositorio($pdo);
$usuario_logado = $_SESSION['usuario'] ?? null;
$usuario = (new \model\repositorio\UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);

$erro = $_GET['erro'] ?? null;

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar - SchweizerPine Shopping</title>
    <link rel="stylesheet" href="../../../css/cadastrar.css">
</head>

<body>

<?php include('header.html'); ?>

<main>
    <section class="cadastro-container">
        <div class="form-box">
            <?php if ($usuario_logado) : ?>
                <span><?php echo htmlspecialchars($usuario->getNome()) ?></span>
            <?php else: ?>
                <a href="login.php" class="btn-login">❮ Login</a>
            <?php endif;
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
                <input type="text" id="nome" name="nome" placeholder="Digite seu nome" required>

                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail" required>

                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" placeholder="Digite seu CPF (apenas números)" required>

                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>

                <label for="confirmar">Confirmar Senha</label>
                <input type="password" id="confirmar" name="confirmar" placeholder="Repita sua senha" required>


                <label for="tipo">Tipo de Funcionário</label>
                <select id="tipo" name="cargo" required>
                    <option value="" disabled selected>Selecione o tipo</option>
                    <option value="administrador">Administrador Geral</option>
                    <option value="lojista">Lojista</option>
                    <option value="cinema">Cinema</option>
                    <option value="anuncio">Gerenciador de Anúncios</option>
                </select>

                <button type="submit" class="btn-cadastrar">Cadastrar</button>
            </form>
        </div>
    </section>
</main>

</body>

</html>