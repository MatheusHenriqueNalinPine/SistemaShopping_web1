<?php

use model\servico\Servico;
use model\repositorio\LojaRepositorio;

require_once __DIR__ . "/../../model/repositorio/LojaRepositorio.php";
require_once __DIR__ . "/../../model/servico/loja/Loja.php";
require_once __DIR__ . "/../../model/servico/Servico.php";
require_once __DIR__ . "/../../model/servico/HorarioFuncionamento.php";
require_once __DIR__ . "/../conexao-bd.php";
require_once __DIR__ . "/../../model/servico/loja/TipoLoja.php";

$repositorio = new LojaRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /../../view/login.php");
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$cnpj = trim($_POST['cnpj'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$tipo_loja = trim($_POST['tipo-loja'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$posicao = trim($_POST['posicao'] ?? '');
$imagem = trim($_POST['imagem'] ?? '');
$tipo_imagem = trim($_POST['tipo_imagem'] ?? '');
$horario_inicial = trim($_POST['horario_inicial'] ?? '');
$horario_final = trim($_POST['horario_final'] ?? '');

if ($nome === '' || $email === '' || $cnpj === '' || $telefone === '' || $categoria === '' || $descricao === '') {
    header("Location: ../../view/cadastrar.php?erro=campos-vazios");
    exit;
}

if($repositorio->cnpjExists($cnpj)) {
    header("Location: ../../view/cadastrar-loja.php?erro=cnpj-repetido");
    exit;
}

$repositorio->salvar(new Loja(0, $nome, $descricao, $imagem, $tipo_imagem, $posicao, $telefone, $cnpj, $categoria,
    TipoLoja::from($tipo_loja), new HorarioFuncionamento($horario_inicial, $horario_final)));

header("Location: ../../view/loja-dashboard.php");
exit;