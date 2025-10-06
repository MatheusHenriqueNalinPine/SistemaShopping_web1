<?php
session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if(!$usuario_logado) {
    header('Location: login.php?erro=deslogado');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Loja - SchweizerPine Shopping</title>
    <link rel="stylesheet" href="css/cadastrar.css">
    <link rel="stylesheet" href="css/cadastrar-loja.css">
</head>

<body>

    <header>
        <div class="logo">
            <img src="img/logoShopping.png" alt="Logo SchweizerPine Shopping">
        </div>
        <nav>
            <a href="index.html">Início</a>
            <a href="#">Novidades</a>
            <a href="#">Cinema</a>
            <a href="#">Lojas</a>
            <a href="#">Gastronomia</a>
            <a href="#">Mapa</a>
            <a href="#">Fale Conosco</a>
        </nav>
        <a href="src/view/login.php" class="btn-login">Login</a>
    </header>

    <main>
        <section class="cadastro-container">
            <div class="form-box">
                <h2>Cadastro de Loja</h2>
                <form>
                    <label for="nomeLoja">Nome da Loja</label>
                    <input type="text" id="nomeLoja" placeholder="Digite o nome da loja" required>

                    <label for="cnpj">CNPJ</label>
                    <input type="text" id="cnpj" placeholder="Digite o CNPJ" required>

                    <label for="emailLoja">E-mail da Loja</label>
                    <input type="email" id="emailLoja" placeholder="Digite o e-mail da loja" required>

                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" placeholder="(00) 00000-0000" required>

                    <label for="categoria">Categoria</label>
                    <select id="categoria" required>
                        <option value="" disabled selected>Selecione a categoria</option>
                        <option value="roupas">Roupas</option>
                        <option value="calcados">Calçados</option>
                        <option value="alimentacao">Alimentação</option>
                        <option value="eletronicos">Eletrônicos</option>
                        <option value="acessorios">Acessórios</option>
                        <option value="servicos">Serviços</option>
                    </select>

                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" placeholder="Descreva sua loja..." rows="4"></textarea>

                    <label for="horario">Horário de Funcionamento</label>
                    <input type="text" id="horario" placeholder="Ex: 10h às 22h" required>


                    <a href="src/view/loja-dashboard.php" class="btn-cadastrarLoja">Cadastrar Loja</a>

                </form>
            </div>
        </section>
    </main>

</body>

</html>
