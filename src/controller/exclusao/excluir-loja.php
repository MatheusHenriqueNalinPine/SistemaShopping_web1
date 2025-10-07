<?php

use model\repositorio\LojaRepositorio;

require_once __DIR__ . "/../../model/repositorio/LojaRepositorio.php";
require_once __DIR__ . "/../../model/servico/loja/Loja.php";
require_once __DIR__ . "/../../model/servico/Servico.php";
require_once __DIR__ . "/../../model/servico/HorarioFuncionamento.php";
require_once __DIR__ . "/../conexao-bd.php";
require_once __DIR__ . "/../../model/servico/loja/TipoLoja.php";

$repositorio = new LojaRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../../view/login.php");
    exit;
}

$repositorio->excluirLoja($_POST['id']);

header("Location: ../../view/loja-dashboard.php");
exit;