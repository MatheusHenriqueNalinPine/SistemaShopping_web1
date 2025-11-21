<?php

use model\repositorio\CategoriaAnuncioRepositorio;

require_once __DIR__ . "/../../model/repositorio/CategoriaAnuncioRepositorio.php";
require_once __DIR__ . "/../conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new CategoriaAnuncioRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/sessoes/login.php");
    exit;
}

if (!$repositorio->isCategoriaUsed($_POST['id'])) {
    $repositorio->excluir($_POST['id']);
} else {
    header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/categoria/categoria-anuncio-dashboard.php?erro=usada");
    exit;
}

header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/categoria/categoria-anuncio-dashboard.php");
exit;