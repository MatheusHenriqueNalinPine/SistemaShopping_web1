<?php

/*Refs:
strlen(): https://www.php.net/manual/pt_BR/function.strlen.php*/

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

$usuarioExistente = $repositorio->buscarPorEmail($email);

if (!$usuarioExistente) {
    $id = 0;
} else {
    $id = $usuarioExistente->getId();
}

if ($nome === '' || $email === '' || $senha === '') {
    header("Location: ../../view/cadastrar.php?erro=campos-vazios");
    exit;
}

if ($repositorio->cpfExists($cpf)) {
    header("Location: ../../view/cadastrar.php?erro=cpf-repetido");
    exit;
}

if ($repositorio->emailExists($email)) {
    header("Location: ../../view/cadastrar.php?erro=email-repetido");
    exit;
}

if (strlen($cpf) != 11) {
    header("Location: ../../view/cadastrar.php?erro=cpf-invalido");
    exit;
}

$repositorio->salvar($nome, $email, $senha, $cpf, $cargo);

header("Location: ../../view/login.php?sucess=true");
exit;