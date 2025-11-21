<?php

use model\repositorio\AnuncioRepositorio;
use model\servico\anuncio\Anuncio;
use model\servico\anuncio\FormatoAnuncio;

require_once __DIR__ . "/../../model/repositorio/AnuncioRepositorio.php";
require_once __DIR__ . "/../../model/repositorio/CategoriaAnuncioRepositorio.php";
require_once __DIR__ . "/../../model/servico/anuncio/Anuncio.php";
require_once __DIR__ . "/../../model/servico/Servico.php";
require_once __DIR__ . "/../conexao-bd.php";
require_once __DIR__ . "/../../model/servico/anuncio/FormatoAnuncio.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new AnuncioRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/sessoes/login.php");
    exit;
}

$id = $_POST['id'];
$nome = trim($_POST['nome'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$formato_anuncio = trim($_POST['formato'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$formatoAnuncio = FormatoAnuncio::from($formato_anuncio);


$imagem = $_POST['imagem_existente'] ?? '';
$nomeImagem = $_POST['imagem_existente'] ?? '';
$tipoImagem = $_POST['tipo_imagem_existente'] ?? 'image/png';
$urlImagem = $_POST['url_imagem_existente'] ?? '';

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $tmpPath = $_FILES['imagem']['tmp_name'];
    $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $newFilename = uniqid('anuncio_') . ($ext ? '.' . $ext : '');
    $uploadDir = __DIR__ . '/../../../img/anuncios/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    $destPath = $uploadDir . $newFilename;

    if (move_uploaded_file($tmpPath, $destPath)) {
        $fileContents = file_get_contents($destPath);
        $imagem = base64_encode($fileContents);
        $nomeImagem = $newFilename;
        $tipoImagem = mime_content_type($destPath) ?: $tipoImagem;
        $urlImagem = 'img/anuncios/' . $newFilename;
    }
}
if ($nome === '' || $formato_anuncio == '' || $categoria === '' || $descricao === '') {
    header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/cadastrar-anuncio.php?erro=campos-vazios");
    exit;
}

if ($id == 0) {
    $repositorio->salvar($nome, $descricao, $imagem, $tipoImagem, $nomeImagem, $urlImagem, new DateTime($data_registro ?? 'now'), $formatoAnuncio, $categoria);
} else {
    $repositorio->atualizar(new Anuncio($id, $nome, $descricao, $imagem, $tipoImagem, $nomeImagem, $urlImagem, new DateTime($data_registro ?? 'now'), $formatoAnuncio, (new \model\repositorio\CategoriaAnuncioRepositorio($pdo))->buscarPorId($categoria)));
}

header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/anuncio-dashboard.php");
exit;