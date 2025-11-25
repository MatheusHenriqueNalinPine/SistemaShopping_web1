<?php

use model\repositorio\FilmeRepositorio;
use model\servico\filme\Filme;
use model\servico\filme\FormatoFilme;

require_once __DIR__ . "/../../model/repositorio/FilmeRepositorio.php";
require_once __DIR__ . "/../../model/repositorio/CategoriaFilmeRepositorio.php";
require_once __DIR__ . "/../../model/servico/filme/Filme.php";
require_once __DIR__ . "/../../model/servico/Servico.php";
require_once __DIR__ . "/../conexao-bd.php";
require_once __DIR__ . "/../../model/servico/filme/FormatoFilme.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new FilmeRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/sessoes/login.php");
    exit;
}

$id = intval($_POST['id'] ?? 0);
$titulo = trim($_POST['titulo'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$formato_filme = trim($_POST['formato'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$data_lancamento = trim($_POST['data_lancamento'] ?? '');
$duracao = trim($_POST['duracao'] ?? '');
$formatoFilme = FormatoFilme::from($formato_filme);


$imagem = $_POST['imagem_existente'] ?? '';
$nomeImagem = $_POST['imagem_existente'] ?? '';
$tipoImagem = $_POST['tipo_imagem_existente'] ?? 'image/png';
$urlImagem = $_POST['url_imagem_existente'] ?? '';

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $tmpPath = $_FILES['imagem']['tmp_name'];
    $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $newFilename = uniqid('filme_') . ($ext ? '.' . $ext : '');
    $uploadDir = __DIR__ . '/../../../img/filmes/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    $destPath = $uploadDir . $newFilename;

    if (move_uploaded_file($tmpPath, $destPath)) {
        $fileContents = file_get_contents($destPath);
        $imagem = base64_encode($fileContents);
        $nomeImagem = $newFilename;
        $tipoImagem = mime_content_type($destPath) ?: $tipoImagem;
        $urlImagem = 'img/filmes/' . $newFilename;
    }
}


if ($titulo === '' || $formato_filme === '' || $categoria === '' || $descricao === '') {
    $redirect = $id === 0
        ? "/SistemaShopping_web1/src/view/administrativo/filme/cadastrar-filme.php?erro=campos-vazios"
        : "/SistemaShopping_web1/src/view/administrativo/filme/editar-filme.php?erro=campos-vazios";
    header("Location: $redirect");
    exit;
}


if ($id === 0) {
   
    $filmeObj = new Filme(0, $titulo, $descricao, $imagem, $tipoImagem, $nomeImagem, $urlImagem, new DateTime($data_lancamento ?: 'now'), $duracao, $formatoFilme, $categoria);
    $repositorio->salvar($filmeObj);
} else {
    
    $categoriaObj = (new \model\repositorio\CategoriaFilmeRepositorio($pdo))->buscarPorId($categoria);
    $filmeObj = new Filme($id, $titulo, $descricao, $imagem, $tipoImagem, $nomeImagem, $urlImagem, new DateTime($data_lancamento ?: 'now'), $duracao, $formatoFilme, $categoriaObj);
    $repositorio->atualizar($filmeObj);
}

header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php");
exit;
