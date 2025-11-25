<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
$telefone = trim($_POST['telefone'] ?? '');
$categoria = trim($_POST['categoria'] ?? '');
$tipo_loja = trim($_POST['tipo-loja'] ?? '');
$descricao = trim($_POST['descricao'] ?? '');
$posicao = trim($_POST['posicao'] ?? '');
$data_registro = trim($_POST['data_registro'] ?? '');
$horarios_iniciais = array_map('trim', $_POST['abertura'] ?? []);
$horarios_finais = array_map('trim', $_POST['fechamento'] ?? []);
$tipoLoja = TipoLoja::from($tipo_loja);

$imagem = $_POST['imagem_existente'] ?? '';
$nomeImagem = $_POST['imagem_existente'] ?? '';
$tipoImagem = $_POST['tipo_imagem_existente'] ?? 'image/png';
$urlImagem = $_POST['url_imagem_existente'] ?? '';

if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
    $tmpPath = $_FILES['imagem']['tmp_name'];
    $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $newFilename = uniqid('loja_') . ($ext ? '.' . $ext : '');
    $uploadDir = __DIR__ . '/../../../img/lojas/';
    if (!file_exists($uploadDir)) mkdir($uploadDir, 0777, true);
    $destPath = $uploadDir . $newFilename;

    if (move_uploaded_file($tmpPath, $destPath)) {
        $fileContents = file_get_contents($destPath);
        $imagem = base64_encode($fileContents);
        $nomeImagem = $newFilename;
        $tipoImagem = mime_content_type($destPath) ?: $tipoImagem;
        $urlImagem = 'img/lojas/' . $newFilename;
    }
}

if ($id == 0) {
    if ($nome === '' || $cnpj === '' || $telefone === '' || $categoria === '' || $descricao === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/loja/cadastrar-loja.php?erro=campos-vazios");
        exit;
    }

    if ($repositorio->cnpjExists($cnpj)) {
        header("Location: /SistemaShopping_web1/src/view/administrativo/loja/cadastrar-loja.php?erro=cnpj-repetido");
        exit;
    }
    $novoId = $repositorio->salvar(new Loja(0, $nome, $descricao, $imagem, $tipoImagem, $nomeImagem, $urlImagem, new DateTime($data_registro ?? 'now'), $posicao, $telefone, $cnpj, $categoria,
        TipoLoja::from($tipo_loja), horariosFuncionamento($horarios_iniciais, $horarios_finais)));

} else {
    if ($nome === ''|| $cnpj === '' || $telefone === '' || $categoria === '' || $descricao === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/loja/editar-loja.php?erro=campos-vazios");
        exit;
    }
    $repositorio->alterarLoja(new Loja($id, $nome, $descricao, $imagem, $tipoImagem, $nomeImagem, $urlImagem, new DateTime($data_registro ?? 'now'), $posicao, $telefone, $cnpj, $categoria,
        $tipoLoja, horariosFuncionamento($horarios_iniciais, $horarios_finais)));

    header("Location: /SistemaShopping_web1/src/view/administrativo/loja/loja-dashboard.php");
    exit;
}

header("Location: /SistemaShopping_web1/src/view/administrativo/loja/loja-dashboard.php");
exit;


function horariosFuncionamento(array $aberturas, array $fechamentos): array
{
    $horarios_funcionamento = [];
    foreach ($aberturas as $dia => $horario_inicial) {
        $hora_fechamento = $fechamentos[$dia] ?? '';
        $horarios_funcionamento[] = new HorarioFuncionamento($horario_inicial, $hora_fechamento, $dia);
    }
    return $horarios_funcionamento;
}