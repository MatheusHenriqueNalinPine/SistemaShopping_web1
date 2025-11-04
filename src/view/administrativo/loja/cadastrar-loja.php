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
$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);

$erro = $_GET['erro'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Loja - SchweizerPine Shopping</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/cadastrar.css">
</head>

<body>
<?php include('../menu.php') ?>
<main>
    <section class="cadastro-container">
        <div class="form-box">
            <?php if (isset($_GET['erro']) && $_GET['erro'] === 'campos-vazios'): ?>
                <p class="mensagem-erro">Não deixe os campos vazios.</p>
            <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'cnpj-repetido'): ?>
                <p class="mensagem-erro">CNPJ Repetido, tente novamente.</p>
            <?php elseif (isset($_GET['erro']) && $_GET['erro'] === 'cnpj-invalido'): ?>
                <p class="mensagem-erro">Apenas números (14) no CNPJ, tente novamente.</p>
            <?php endif; ?>
            <h2>Cadastro de Loja</h2>
            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_loja.php" method="post">
                <label for="nomeLoja">Nome da Loja</label>
                <input type="text" id="nomeLoja" name="nome" placeholder="Digite o nome da loja" required>

                <label for="cnpj">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" placeholder="Digite o CNPJ (apenas números)" required>

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
                    <option value="restaurante" disabled selected>Selecione a categoria</option>
                    <option value="loja">Loja</option>
                    <option value="restaurante">Restaurante</option>
                </select>

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" placeholder="Descreva sua loja..." rows="4"></textarea>

                <label for="posicao">Posição</label>
                <input type="text" id="posicao" name="posicao" placeholder="Ex.: P3L32"></input>

                <label for="imagem">Imagem (.png)</label>
                <input type="file" id="imagem" name="imagem" accept="image/png">

                <br/>

                <h3>Horário de Funcionamento</h3>

                <table class="horario">
                    <thead>
                    <tr>
                        <th>Dia</th>
                        <th>Abertura</th>
                        <th>Fechamento</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="dia">Domingo</td>
                        <td><input type="time" name="abertura[domingo]"></td>
                        <td><input type="time" name="fechamento[domingo]"></td>
                    </tr>
                    <tr>
                        <td class="dia">Segunda</td>
                        <td><input type="time" name="abertura[segunda]"></td>
                        <td><input type="time" name="fechamento[segunda]"></td>
                    </tr>
                    <tr>
                        <td class="dia">Terça</td>
                        <td><input type="time" name="abertura[terca]"></td>
                        <td><input type="time" name="fechamento[terca]"></td>
                    </tr>
                    <tr>
                        <td class="dia">Quarta</td>
                        <td><input type="time" name="abertura[quarta]"></td>
                        <td><input type="time" name="fechamento[quarta]"></td>
                    </tr>
                    <tr>
                        <td class="dia">Quinta</td>
                        <td><input type="time" name="abertura[quinta]"></td>
                        <td><input type="time" name="fechamento[quinta]"></td>
                    </tr>
                    <tr>
                        <td class="dia">Sexta</td>
                        <td><input type="time" name="abertura[sexta]"></td>
                        <td><input type="time" name="fechamento[sexta]"></td>
                    </tr>
                    <tr>
                        <td class="dia">Sábado</td>
                        <td><input type="time" name="abertura[sabado]"></td>
                        <td><input type="time" name="fechamento[sabado]"></td>
                    </tr>
                    </tbody>
                </table>

                <input type="hidden" id="id" name="id" value="0"/>
                <input class="btn-cadastrar" type="submit" value="Cadastrar"/>
            </form>
        </div>
    </section>
</main>

</body>

</html>
