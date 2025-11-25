<?php

use model\repositorio\CinemaRepositorio;
use model\servico\filme\Filme;

require_once __DIR__ . "/../../model/repositorio/CinemaRepositorio.php";
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

$repositorio = new CinemaRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/sessoes/login.php");
    exit;
}

$id = $_POST['id'];
$nome = trim($_POST['nome'] ?? '');
$genero = trim($_POST['genero'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$data_registro = trim($_POST['data_registro'] ?? '');

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

if ($id == 0) {
    if ($nome === '' || $genero === '' || $descricao === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/filme/cadastrar-filme.php?erro=campos-vazios");
        exit;
    }

    $repositorio->salvar($nome, $descricao, $imagem, $tipoImagem, $nomeImagem, $urlImagem,$genero);
} else {
    if ($nome === ''|| $genero === '' || $descricao === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/filme/editar-filme.php?erro=campos-vazios");
        exit;
    }
    $repositorio->atualizar(new Filme($id, $nome, $descricao, $imagem, $tipoImagem, $nomeImagem, $urlImagem, new DateTime($data_registro ?? 'now'), $genero));

    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php");
    exit;
}

header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php");
exit;
