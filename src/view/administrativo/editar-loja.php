<?php

use model\repositorio\LojaRepositorio;
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../model/repositorio/LojaRepositorio.php";
require_once __DIR__ . "/../model/servico/loja/Loja.php";
require_once __DIR__ . "/../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../model/usuario/Usuario.php";
require_once __DIR__ . "/../controller/conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if(!$usuario_logado) {
    header('Location: login.php?erro=deslogado');
    exit;
}

$repositorio = new LojaRepositorio($pdo);
$usuario = (new \model\repositorio\UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);

$idLoja = $_GET['id'] ?? null;
$loja = $repositorio->buscarPorId($idLoja);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Loja - SchweizerPine Shopping</title>
    <link rel="stylesheet" href="../../../css/cadastrar.css">
    <link rel="stylesheet" href="../../../css/cadastrar-loja.css">
</head>

<body>

    <header>
        <div class="logo">
            <img src="../../../img/logoShopping.png" alt="Logo SchweizerPine Shopping">
        </div>
        <nav>
            <a href="../../../index.html">Início</a>
            <a href="#">Novidades</a>
            <a href="#">Cinema</a>
            <a href="#">Lojas</a>
            <a href="#">Gastronomia</a>
            <a href="#">Mapa</a>
            <a href="#">Fale Conosco</a>
        </nav>
        <?php if ($usuario_logado) : ?>
            <span><?php echo htmlspecialchars($usuario->getNome()) ?></span>
        <?php else: ?>
            <a href="../sessoes/login.php" class="btn-login">Login</a>
        <?php endif; ?>
    </header>

    <main>
        <section class="cadastro-container">
            <div class="form-box">
                <?php if (isset($_GET['erro']) &&   $_GET['erro'] === 'campos-vazios'): ?>
                    <p class="mensagem-erro">Não deixe os campos vazios.</p>
                <?php endif; ?>
                <h2>Editar Loja</h2>
                <form action="../../controller/cadastro/editar_loja_controller.php" method="post">
                    <label for="nomeLoja">Nome da Loja</label>
                    <input type="text" id="nomeLoja" name="nome" placeholder="Digite o nome da loja" required>

                    <label for="cnpj">CNPJ</label>
                    <input type="text" id="cnpj" name="cnpj" placeholder="Digite o CNPJ" required>

                    <label for="emailLoja">E-mail da Loja</label>
                    <input type="email" id="emailLoja" name="email" placeholder="Digite o e-mail da loja" required>

                    <label for="telefone">Telefone</label>
                    <input type="tel" id="telefone" name="telefone" placeholder="(00) 00000-0000" required>

                    <label for="categoria">Categoria</label>
                    <select id="categoria" name="categoria" required>
                        <option value="" disabled selected>Selecione a categoria</option>
                        <option value="roupas">Roupas</option>
                        <option value="calcados">Calçados</option>
                        <option value="alimentacao">Alimentação</option>
                        <option value="eletronicos">Eletrônicos</option>
                        <option value="acessorios">Acessórios</option>
                        <option value="servicos">Serviços</option>
                    </select>

                    <label for="tipo-loja">Loja ou Restaurante?</label>
                    <select id="tipo-loja" name="tipo-loja" required>
                        <option value="selecione" disabled selected>Selecione a categoria</option>
                        <option value="loja">Loja</option>
                        <option value="restaurante">Restaurante</option>
                    </select>

                    <label for="descricao">Descrição</label>
                    <textarea id="descricao" name="descricao" placeholder="Descreva sua loja..." rows="4"></textarea>

                    <label for="posicao">Posição</label>
                    <input type="text" id="posicao" name="posicao" placeholder="Ex.: P3L32"></input>

                    <label for="imagem">Imagem (.png)</label>
                    <input type="file" id="imagem" name="imagem" accept="image/png">

                    <input type="hidden" id="tipo-imagem" name="tipo-imagem" value="image/png">

                    <label for="horario">Horário Inicial de Funcionamento</label>
                    <input type="text" id="horario_inicial" name="horario_inicial" placeholder="Ex: 10h" required>

                    <label for="horario">Horário Final de Funcionamento</label>
                    <input type="text" id="horario_final" name="horario_final" placeholder="Ex: 22h" required>

                    <input type="hidden" name="id" value="<?= $loja->getId() ?>">

                    <input type="submit" value="Editar"/>
                </form>
            </div>
        </section>
    </main>

</body>

</html>
