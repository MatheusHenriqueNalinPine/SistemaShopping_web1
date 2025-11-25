<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use model\repositorio\CategoriaLojaRepositorio;

require_once __DIR__ . "/../../model/repositorio/CategoriaLojaRepositorio.php";
require_once __DIR__ . "/../conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new CategoriaLojaRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/sessoes/login.php");
    exit;
}

$id = $_POST['id'];
$categoria = trim($_POST['categoria'] ?? '');

if ($id == 0) {
    if ($categoria === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/loja/horarios/cadastrar-horarios.php?erro=campos-vazios");
        exit;
    }
    $repositorio->salvar($categoria);

} else {
    if ($categoria === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/loja/horarios/editar-horarios.php?erro=campos-vazios");
        exit;
    }
    $repositorio->alterar($id, $categoria);

    header("Location: /SistemaShopping_web1/src/view/administrativo/loja/horarios/horarios-loja-dashboard.php?id=" . urlencode($id));
    exit;
}

header("Location: /SistemaShopping_web1/src/view/administrativo/loja/horarios/horarios-loja-dashboard.php");
exit;
