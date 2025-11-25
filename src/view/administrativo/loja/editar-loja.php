<?php

use model\repositorio\CategoriaLojaRepositorio;
use model\repositorio\LojaRepositorio;
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../../model/repositorio/LojaRepositorio.php";
require_once __DIR__ . "/../../../model/servico/loja/Loja.php";
require_once __DIR__ . "/../../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../../model/repositorio/CategoriaLojaRepositorio.php";
require_once __DIR__ . "/../../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../../controller/conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new LojaRepositorio($pdo);
$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);

$idLoja = $_GET['id'] ?? null;
$loja = $repositorio->buscarPorId($idLoja);

$cargo = $usuario->getCargo();
if ($cargo == Cargo::Funcionario_cinema || $cargo == Cargo::Gerenciador_anuncio) {
    header('Location: /SistemaShopping_web1/src/view/administrativo/administrativo.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <title>Editar Loja - SchweizerPine Shopping</title>
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
            <h2>Editar Loja</h2>
            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_loja.php" method="post"
                  enctype="multipart/form-data">
                <label for="nomeLoja">Nome da Loja</label>
                <input type="text" id="nomeLoja" name="nome" placeholder="Digite o nome da loja"
                       value="<?= $loja->getNome() ?>" required>

                <label for="cnpj">CNPJ</label>
                <input type="text" id="cnpj" name="cnpj" placeholder="Digite o CNPJ" value="<?= $loja->getCnpj() ?>"
                       required>

                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone"
                       placeholder="(00) 00000-0000" value="<?= $loja->getTelefoneContato() ?>" required>

                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" required>
                    <option value="" disabled selected>Selecione a categoria</option>
                    <?php $categorias = (new CategoriaLojaRepositorio($pdo))->buscarTodas();
                    foreach ($categorias as $categoria): ?>
                        <option value="<?= htmlspecialchars($categoria['id']) ?>"
                                <?= $categoria['id'] == $loja->getCategoria() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($categoria['horarios']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="tipo-loja">Loja ou Restaurante?</label>
                <select id="tipo-loja" name="tipo-loja" required>
                    <option value="selecione" disabled selected>Selecione a categoria</option>
                    <option value="loja" <?= $loja->getTipoLoja() === TipoLoja::Loja ? 'selected' : '' ?>>Loja</option>
                    <option value="restaurante" <?= $loja->getTipoLoja() === TipoLoja::Restaurante ? 'selected' : '' ?>>
                        Restaurante
                    </option>
                </select>

                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" placeholder="Descreva sua loja..."
                          rows="4"><?= $loja->getDescricao() ?></textarea>

                <label for="posicao">Posição</label>
                <input type="text" id="posicao" name="posicao" placeholder="Ex.: P3L32"
                       value="<?= $loja->getPosicao() ?>" required>

                <input type="file" name="imagem" accept="image/*">

                <?php if (!empty($loja->getNomeImagem())): ?>
                    <div class="preview-imagem">
                        <p>Imagem atual: <?= htmlspecialchars($loja->getNomeImagem()) ?></p>
                        <img src="<?= htmlspecialchars('/SistemaShopping_web1/img/lojas/' . $loja->getNomeImagem()) ?>"
                             alt="Imagem da loja" style="max-width:200px;">
                        <input type="hidden" name="imagem_existente"
                               value="<?= htmlspecialchars($loja->getNomeImagem()) ?>">
                        <input type="hidden" name="tipo_imagem_existente"
                               value="<?= htmlspecialchars($loja->getTipoImagem()) ?>">
                        <input type="hidden" name="url_imagem_existente"
                               value="<?= htmlspecialchars($loja->getUrlImagem()) ?>">
                    </div>
                <?php endif; ?>

                <table class="horario">
                    <thead>
                    <tr>
                        <th>Dia</th>
                        <th>Abertura</th>
                        <th>Fechamento</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($loja->getHorarioFuncionamento() as $horario): ?>
                        <tr>
                            <td class="dia"><?= htmlspecialchars($horario->getDiaSemana()) ?></td>
                            <td><input type="time" name="abertura[<?= $horario->getDiaSemana() ?>]"
                                       value="<?= $horario->getHorarioInicial() ?>"></td>
                            <td><input type="time" name="fechamento[<?= $horario->getDiaSemana() ?>]"
                                       value="<?= $horario->getHorarioFinal() ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <input type="hidden" name="id" value="<?= $loja->getId() ?>">

                <input type="submit" value="Editar"/>
            </form>
        </div>
    </section>
</main>

</body>

</html>
