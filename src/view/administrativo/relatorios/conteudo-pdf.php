<?php
//Referências:
//usar css de arquivo externo no DomPDF: https://github.com/dompdf/dompdf/issues/1562#issuecomment-333391364

use model\repositorio\AnuncioRepositorio;
use model\repositorio\CinemaRepositorio;
use model\repositorio\LojaRepositorio;
use model\repositorio\UsuarioRepositorio;

require "../../../src/controller/conexao-bd.php";
require "../../../src/Model/usuario/Usuario.php";
require "../../../src/Model/servico/loja/Loja.php";
require "../../../src/Model/servico/filme/Filme.php";
require "../../../src/Model/servico/anuncio/Anuncio.php";
require "../../../src/Model/Repositorio/AnuncioRepositorio.php";
require "../../../src/Model/Repositorio/LojaRepositorio.php";
require "../../../src/Model/Repositorio/CinemaRepositorio.php";
require "../../../src/Model/Repositorio/UsuarioRepositorio.php";
require "../../../src/Model/Repositorio/CategoriaLojaRepositorio.php";

date_default_timezone_set('America/Sao_Paulo');
$rodapeDataHora = date('d/m/Y H:i');

$itens = [];
$repositorio = null;

if ($tipo == "usuarios") {
    $repositorio = new UsuarioRepositorio($pdo);
    $itens = $repositorio->buscarTodos();
} else if ($tipo == "lojas") {
    $repositorio = new LojaRepositorio($pdo);
    $itens = $repositorio->buscarlojas();
} else if ($tipo == "filmes") {
    $repositorio = new CinemaRepositorio($pdo);
    $itens = $repositorio->buscarTodos();
} else if ($tipo == "anuncios") {
    $repositorio = new AnuncioRepositorio($pdo);
    $itens = $repositorio->buscarTodos();
}

$imagePath = $_SERVER["DOCUMENT_ROOT"] . "/SistemaShopping_web1/img/logoShopping.jpg";
$imageData = base64_encode(file_get_contents($imagePath));
$imageSrc = "data:image/png;base64,$imageData";
?>

<head>
    <meta charset="UTF-8">
</head>
<?php $cssPath = $_SERVER["DOCUMENT_ROOT"] . "/SistemaShopping_web1/css/relatorio.css";
$css = file_get_contents($cssPath);

echo "<style>$css</style>"; ?>
<img src="<?= $imageSrc ?>" class="pdf-img" alt="logo-shopping">

<h3>Listagem de <?= $tipo ?></h3>

<table>
    <thead>
    <tr>
        <?php if ($tipo == "usuarios"): ?>
            <th>Nome</th>
            <th>E-mail</th>
            <th>CPF</th>
            <th>Cargo</th>
        <?php else: ?>
            <th>Nome</th>
            <th>Descricão</th>
            <th>Data registro</th>
            <?php if ($tipo == "lojas"): ?>
                <th>CNPJ</th>
                <th>Telefone</th>
                <th>Posição</th>
                <th>Loja ou restaurante</th>
                <th>Categoria</th>
            <?php elseif ($tipo == "filmes"): ?>
                <th>Gênero</th>
                <th>N° horários exibição</th>
            <?php elseif ($tipo == "anuncios"): ?>
                <th>Formato</th>
                <th>Categoria</th>
            <?php endif; ?>
        <?php endif; ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($itens as $item): ?>
        <tr>
            <?php if ($tipo == "usuarios"): ?>
                <td><?= $item->getNome() ?></td>
                <td><?= $item->getEmail() ?></td>
                <td><?= $item->getCpf() ?></td>
                <td><?= $item->getCargo()->value ?></td>
            <?php else: ?>
                <td><?= $item->getNome() ?></td>
                <td><?= $item->getDescricao() ?></td>
                <td><?= $item->getDataRegistro()->format('d/m/Y') ?></td>
                <?php if ($tipo == "lojas"): ?>
                    <td><?= $item->getCnpj() ?></td>
                    <td><?= $item->getTelefoneContato() ?></td>
                    <td><?= $item->getPosicao() ?></td>
                    <td><?= $item->getTipoLoja()->value ?></td>
                    <td><?= (new \model\repositorio\CategoriaLojaRepositorio($pdo))->buscarPorId($item->getCategoria()) ?></td>
                <?php elseif ($tipo == "filmes"): ?>
                    <td><?= $item->getGenero() ?></td>
                    <td><?= $repositorio->numeroHorariosExibicao($item->getId()) ?></td>
                <?php elseif ($tipo == "anuncios"): ?>
                    <td><?= $item->getFormatoAnuncio()->value ?></td>
                    <td><?= $repositorio->getCategoriaById(intval($item->getCategoriaAnuncio())) ?></td>
                <?php endif; ?>
            <?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<div class="pdf-footer">
    Gerado em: <?= htmlspecialchars($rodapeDataHora) ?>
</div>

