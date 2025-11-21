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

$id = trim($_POST["id"] ?? null);
$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar = $_POST['confirmar'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$cargo = $_POST['cargo'] ?? '';


if ($id == 0) {
    if ($nome === '' || $email === '' || $senha === '') {
        header("Location: /SistemaShopping_web1/src/view/sessoes/cadastrar.php?erro=campos-vazios");
        exit;
    }

    if ($repositorio->cpfExists($cpf)) {
        header("Location: /SistemaShopping_web1/src/view/sessoes/cadastrar.php?erro=cpf-repetido");
        exit;
    }

    if ($confirmar !== $senha) {
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
} else {
    if ($senha === '') {
        $senha = $repositorio->buscarPorId($id)->getSenha();
        $confirmar = $senha;
    }

    if ($nome === '' || $email === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/usuario/editar-usuario.php?erro=campos-vazios");
        exit;
    }

    if ($repositorio->cpfExists($cpf) && !$repositorio->cpfIs($cpf, $id)) {
        header("Location: /SistemaShopping_web1/src/view/administrativo/usuario/editar-usuario.php?erro=cpf-repetido");
        exit;
    }

    if ($confirmar !== $senha) {
        header("Location: /SistemaShopping_web1/src/view/administrativo/usuario/editar-usuario.php?erro=senhas-diferentes");
        exit;
    }

    if ($repositorio->emailExists($email) && !$repositorio->emailIs($email, $id)) {
        header("Location: /SistemaShopping_web1/src/view/administrativo/usuario/editar-usuario.php?erro=email-repetido");
        exit;
    }

    if (strlen($cpf) != 11) {
        header("Location: /SistemaShopping_web1/src/view/administrativo/usuario/editar-usuario.php?erro=cpf-invalido");
        exit;
    }

    $usuarioAntigo = $repositorio->buscarPorId($id);

    if (!$usuarioAntigo) {
        session_destroy();
        header("Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado");
        exit;
    }

    if ($usuarioAntigo->getEmail() === $usuario_logado) {
        $_SESSION['usuario'] = $email;
    }

    $repositorio->atualizar((new Usuario($id, $nome, $email, $senha, $cpf, Cargo::from($cargo))));
    header("Location:  /SistemaShopping_web1/src/view/administrativo/usuario/usuarios-dashboard.php");
    exit;
}