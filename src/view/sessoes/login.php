<?php
session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;
$erro = $_GET['erro'] ?? '';

if($usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/administrativo/administrativo.php');
    exit;
}

$sucesso_cadastro = $_GET['sucess'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/login.css">
</head>

<body>

<?php include ('header.html') ?>

<main>
        <section class="login-container">
            <div class="logo-box">
                <img src="/SistemaShopping_web1/img/logoShopping.png" alt="Logo Shopping" class="logo-circle">
                <a href="cadastrar.php" class="btn-cadastrar">Cadastrar funcionario</a>
            </div>
            <div class="form-box">
                <?php if($sucesso_cadastro) :?>
                    <p class="cadastro-sucedido">Cadastro sucedido, efetue o Login.</p>
                <?php endif; ?>
                <h2>Bem vindo funcionário</h2>
                <form action="/SistemaShopping_web1/src/controller/autenticacao/autenticar.php" method="post">
                    <label for="email">E-mail</label>
                    <input type="text" id="email" name="email" placeholder="Digite seu e-mail">

                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite a senha">

                    <a href="#" class="forgot">Esqueci minha senha</a>

                    <button type="submit" class="btn-enviar">Enviar</button>
                </form>
            </div>
        </section>
</main>
</body>

</html>