<?php

use model\repositorio\CinemaRepositorio;

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php?erro=metodo");
    exit;
}

$id = $_POST['id'] ?? null;
if (empty($id)) {
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php?erro=sem_id");
    exit;
}


$srcDir = dirname(__DIR__, 2);
$repoPath = $srcDir . '/model/repositorio/CinemaRepositorio.php';
$filmePath = $srcDir . '/model/servico/filme/Filme.php';
$conexaoPath = $srcDir . '/controller/conexao-bd.php';

if (!file_exists($conexaoPath)) {
    error_log("Arquivo de conexão não encontrado em: $conexaoPath");
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php?erro=conexao");
    exit;
}
require_once $conexaoPath;

if (file_exists($repoPath)) {
    require_once $repoPath;
} else {
    error_log("FilmeRepositorio.php não encontrado em: $repoPath — será usado fallback PDO para exclusão.");
}


if (file_exists($filmePath)) {
    error_log("Filme.php encontrado em $filmePath — não será incluído aqui para evitar dependências faltantes.");
} else {
    error_log("Filme.php não encontrado em: $filmePath");
}

$repositorio = new CinemaRepositorio($pdo);
$sucesso = $repositorio->excluir($id);

if(!$sucesso) {

    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php?erro=exclusao&id=".$id);
    exit;
}

header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php");
exit;
