<?php

use Cassandra\Date;
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
$data_registro = trim($_POST['data_registro'] ?? '');
$horario_inicial = trim($_POST['horario_inicial'] ?? '');
$horario_final = trim($_POST['horario_final'] ?? '');
$dia_semana = trim($_POST['dia_semana'] ?? '');
$tipoLoja = TipoLoja::from($tipo_loja);


$imagem = '';
$tipoImagem = 'image/png';
$nomeImagem = '';
$urlImagem = '';

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $tmpPath = $_FILES['imagem']['tmp_name'];
    $originalName = $_FILES['imagem']['name'];
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    $newFilename = uniqid('loja_') . ($ext ? '.' . $ext : '');
    $uploadDir = __DIR__ . '/../../../img/lojas/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    $destPath = $uploadDir . $newFilename;

    if (move_uploaded_file($tmpPath, $destPath)) {
        
        $fileContents = file_get_contents($destPath);
        if ($fileContents !== false) {
            $imagem = base64_encode($fileContents);
        }
        $tipoImagem = mime_content_type($destPath) ?: $tipoImagem;
        $nomeImagem = $originalName;
     
        $urlImagem = 'img/lojas/' . $newFilename;
    }
}


if ($id == 0) {
    if ($nome === '' || $email === '' || $cnpj === '' || $telefone === '' || $categoria === '' || $descricao === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/loja/cadastrar-loja.php?erro=campos-vazios");
        exit;
    }
   
    if ($repositorio->cnpjExists($cnpj)) {
        header("Location: /SistemaShopping_web1/src/view/administrativo/loja/cadastrar-loja.php?erro=cnpj-repetido");
        exit;
    }
    $novoId = $repositorio->salvar(new Loja(0, $nome, $descricao, $imagem, $nomeImagem, $urlImagem, new Date($data_registro ?? 'now'), $posicao, $telefone, $cnpj, $categoria,
        TipoLoja::from($tipo_loja), new HorarioFuncionamento($horario_inicial, $horario_final, $dia_semana)));

} else {
    if ($nome === '' || $email === '' || $cnpj === '' || $telefone === '' || $categoria === '' || $descricao === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/loja/editar-anuncio.php?erro=campos-vazios");
        exit;
    }
    $repositorio->alterarLoja(new Loja($id, $nome, $descricao, $imagem, $tipoImagem, $nomeImagem, $urlImagem, new Date($data_registro ?? 'now'), $posicao, $telefone, $cnpj, $categoria,
        $tipoLoja, new HorarioFuncionamento($horario_inicial, $horario_final, $dia_semana)));
    
    header("Location: /SistemaShopping_web1/src/view/administrativo/loja/telaDeLoja.php?id=" . urlencode($novoId));
    exit;
}

header("Location: /SistemaShopping_web1/src/view/administrativo/loja/loja-dashboard.php");
exit;