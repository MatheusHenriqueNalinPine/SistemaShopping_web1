<?php

use model\repositorio\HorarioFilmeRepositorio;

require_once __DIR__ . "/../../model/repositorio/HorarioFilmeRepositorio.php";
require_once __DIR__ . "/../conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;
if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/horarios-filme-dashboard.php?erro=metodo");
    exit;
}

$id_filme = $_POST['id_filme'] ?? null;
$data_hora = $_POST['data_hora'] ?? null;
$sala_filme = $_POST['sala_filme'] ?? null;

if (!$id_filme || !$data_hora || !$sala_filme) {
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/horarios-filme-dashboard.php?erro=sem_id");
    exit;
}

$repositorio = new HorarioFilmeRepositorio($pdo);

try {
    $horario = $repositorio->buscarPorChavePrimaria($id_filme, new DateTime($data_hora), $sala_filme);
    if ($horario) {
        $repositorio->excluir($horario);
    } else {
        header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/horarios-filme-dashboard.php?erro=nao_encontrado");
        exit;
    }
} catch (\Exception $e) {
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/horarios-filme-dashboard.php?erro=exclusao");
    exit;
}

header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/horarios-filme-dashboard.php");
exit;
