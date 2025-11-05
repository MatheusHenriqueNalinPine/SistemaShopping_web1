<?php

/*Refs:
strlen(): https://www.php.net/manual/pt_BR/function.strlen.php*/

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../controller/conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

$repositorio = new UsuarioRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/sessoes/login.php");
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$cargo = $_POST['cargo'] ?? '';

$usuarioExistente = $repositorio->buscarPorEmail($email);

if (!$usuarioExistente) {
    $id = 0;
} else {
    $id = $usuarioExistente->getId();
}

if ($nome === '' || $email === '' || $senha === '') {
    header("Location: /SistemaShopping_web1/src/view/sessoes/cadastrar.php?erro=campos-vazios");
    exit;
}

if ($repositorio->cpfExists($cpf)) {
    header("Location: /SistemaShopping_web1/src/view/sessoes/cadastrar.php?erro=cpf-repetido");
    exit;
}

if($confirmar !== $senha){
    header("Location: /SistemaShopping_web1/src/view/sessoes/cadastrar.php?erro=senhas-diferentes");
    exit;
}

if ($repositorio->emailExists($email)) {
    header("Location: /SistemaShopping_web1/src/view/sessoes/cadastrar.php?erro=email-repetido");
    exit;
}

if (strlen($cpf) != 11) {
    header("Location: /SistemaShopping_web1/src/view/sessoes/cadastrar.php?erro=cpf-invalido");
    exit;
}

$repositorio->salvar($nome, $email, $senha, $cpf, $cargo);

header("Location:  /SistemaShopping_web1/src/view/sessoes/login.php?sucess=true");
exit;