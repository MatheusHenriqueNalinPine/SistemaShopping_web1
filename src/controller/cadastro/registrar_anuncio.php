<?php

use model\repositorio\AnuncioRepositorio;
use model\servico\anuncio\Anuncio;
use model\servico\Servico;
use model\servico\anuncio\FormatoAnuncio;

require_once __DIR__ . "/../../model/repositorio/AnuncioRepositorio.php";
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
$imagem = trim($_POST['imagem'] ?? '');
$data_registro = trim($_POST['data_registro'] ?? '');
$formatoAnuncio = FormatoAnuncio::from($formato_anuncio);

if ($id == 0) {
    if ($nome === '' || $formato_anuncio == '' || $categoria === '' || $descricao === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/cadastrar-anuncio.php?erro=campos-vazios");
        exit;
    }
    $repositorio->salvar($nome, $descricao, $imagem, new DateTime($data_registro ?? 'now'), $formatoAnuncio, $categoria);

} else {
    if ($nome === '' || $formato_anuncio == '' || $categoria === '' || $descricao === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/cadastrar-anuncio.php?erro=campos-vazios");
        exit;
    }
    $repositorio->atualizar(new Anuncio($id, $nome, $descricao, $imagem, new DateTime($data_registro ?? 'now'), $formatoAnuncio, $categoria));
}

header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/anuncio-dashboard.php");
exit;