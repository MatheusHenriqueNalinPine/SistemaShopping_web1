<?php

use model\repositorio\AnuncioRepositorio;

require_once __DIR__ . "/../../model/repositorio/AnuncioRepositorio.php";
require_once __DIR__ . "/../../model/servico/anuncio/Anuncio.php";
require_once __DIR__ . "/../../model/servico/Servico.php";
require_once __DIR__ . "/../conexao-bd.php";

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

$repositorio->excluir($_POST['id']);

header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/anuncio-dashboard.php");
exit;