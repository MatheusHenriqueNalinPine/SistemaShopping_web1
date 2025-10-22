<?php


use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../model/usuario/Usuario.php";
require_once __DIR__ . "/../conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new UsuarioRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/sessoes/login.php");
    exit;
}

if($_POST["id"] != $repositorio->buscarPorEmail($usuario_logado)->getId()){
    $repositorio->excluir($_POST['id']);
} else {
    header("Location: /SistemaShopping_web1/src/view/administrativo/usuario/usuarios-dashboard.php?erro=exclusao");
    exit;
}

header("Location: /SistemaShopping_web1/src/view/administrativo/usuario/usuarios-dashboard.php");
exit;