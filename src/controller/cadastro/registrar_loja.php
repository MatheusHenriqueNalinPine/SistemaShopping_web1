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

$id = $_POST['id'];
$nome = trim($_POST['nome'] ?? '');
$cnpj = trim($_POST['cnpj'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$tipo_loja = trim($_POST['tipo-loja'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$posicao = trim($_POST['posicao'] ?? '');
$imagem = trim($_POST['imagem'] ?? '');
$data_registro = trim($_POST['data_registro'] ?? '');
$horario_inicial = trim($_POST['horario_inicial'] ?? '');
$horario_final = trim($_POST['horario_final'] ?? '');
$tipoLoja = TipoLoja::from($tipo_loja);

if ($id == 0) {
    if ($nome === '' || $email === '' || $cnpj === '' || $telefone === '' || $categoria === '' || $descricao === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/loja/cadastrar-anuncio.php?erro=campos-vazios");
        exit;
    }
    $repositorio->salvar(new Loja(0, $nome, $descricao, $imagem, new DateTime($data_registro ?? 'now'), $posicao, $telefone, $cnpj, $categoria,
        TipoLoja::from($tipo_loja), new HorarioFuncionamento($horario_inicial, $horario_final)));

} else {
    if ($nome === '' || $email === '' || $cnpj === '' || $telefone === '' || $categoria === '' || $descricao === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/loja/editar-anuncio.php?erro=campos-vazios");
        exit;
    }
    $repositorio->alterarLoja(new Loja($id, $nome, $descricao, $imagem, new DateTime($data_registro ?? 'now'), $posicao, $telefone, $cnpj, $categoria,
        $tipoLoja, new HorarioFuncionamento($horario_inicial, $horario_final)));
}

header("Location: /SistemaShopping_web1/src/view/administrativo/loja/anuncio-dashboard.php");
exit;