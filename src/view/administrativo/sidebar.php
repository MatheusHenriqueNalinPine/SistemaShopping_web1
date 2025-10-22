<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../model/repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../../controller/conexao-bd.php';

$usuario_logado = $_SESSION['usuario'] ?? null;
$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/crud-tabela.css">
</head>

<aside class="sidebar">
    <ul>
        <a href="/SistemaShopping_web1/src/view/administrativo/administrativo.php">Administrativo</a>
        <a href="/SistemaShopping_web1/src/view/administrativo/loja/loja-dashboard.php">Lojas</a>
        <a href="/SistemaShopping_web1/src/view/administrativo/anuncio/anuncio-dashboard.php">Anúncios</a>
        <a href="#">Cinema</a>
        <a href="/SistemaShopping_web1/src/view/administrativo/usuario/usuarios-dashboard.php">Funcionários</a>
        <a href="/SistemaShopping_web1/src/controller/autenticacao/logout.php">Sair</a>
    </ul>
</aside>

</html>
