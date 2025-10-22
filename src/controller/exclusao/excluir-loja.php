<?php

use model\repositorio\LojaRepositorio;

require_once __DIR__ . "/../../model/repositorio/LojaRepositorio.php";
require_once __DIR__ . "/../../model/servico/loja/Loja.php";
require_once __DIR__ . "/../../model/servico/Servico.php";
require_once __DIR__ . "/../../model/servico/HorarioFuncionamento.php";
require_once __DIR__ . "/../conexao-bd.php";
require_once __DIR__ . "/../../model/servico/loja/TipoLoja.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new LojaRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/sessoes/login.php");
    exit;
}

$repositorio->excluirLoja($_POST['id']);

header("Location: /SistemaShopping_web1/src/view/administrativo/loja/anuncio-dashboard.php");
exit;