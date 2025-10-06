<?php

use model\repositorio\UsuarioRepositorio;

session_start();

require_once __DIR__ . "/../conexao-bd.php";
require_once __DIR__ . "/../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../model/repositorio/UsuarioRepositorio.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../index.php');
    exit();
}

$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

if ($email === '' || $senha === '') {
    header('Location: login.php?erro=campos-vazios');
    exit;
}

$repo = new UsuarioRepositorio($pdo);
$usuario = $repo->buscarPorEmail($email);


if ($repo->autenticar($email, $senha)) {
    session_regenerate_id(true);
    $perfil = $usuario->getPerfil();
    $_SESSION['usuario'] = $email;
    header('Location: ../../view/administrativo.php');
    exit;
}

header('Location: login.php?erro=dados-incorretos');
exit;