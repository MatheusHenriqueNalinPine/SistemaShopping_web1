<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../controller/conexao-bd.php";

$repositorio = new UsuarioRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /../../view/login.php");
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$cargo = $_POST['cargo'] ?? '';

$id = $repositorio->buscarPorEmail($email)->getId();

if ($nome === '' || $email === '' || $senha === '') {
    header("Location: ../../view/cadastrar.php?erro=campos");
    exit;
}

$repositorio->salvar(new Usuario($id, $nome, $email, $senha, $cpf, $cargo));

header("Location: ../../view/login.php");
exit;