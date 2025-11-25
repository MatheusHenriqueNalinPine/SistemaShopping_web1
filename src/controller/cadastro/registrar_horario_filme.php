<?php

use model\repositorio\HorarioFilmeRepositorio;
use model\servico\filme\HorarioExibicaoFilme;
use model\servico\filme\FormatoFilme;

require_once __DIR__ . "/../../model/repositorio/HorarioFilmeRepositorio.php";
require_once __DIR__ . "/../../model/servico/filme/HorarioExibicaoFilme.php";
require_once __DIR__ . "/../../model/servico/filme/FormatoFilme.php";
require_once __DIR__ . "/../conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new HorarioFilmeRepositorio($pdo);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/sessoes/login.php");
    exit;
}

$id = $_POST['id'] ?? 0;
$id_filme = $_POST['id_filme'] ?? 0;
$sala = $_POST['sala'] ?? 0;
$formato = $_POST['formato'] ?? '';
$modo_exibicao = $_POST['modo_exibicao'] ?? '';
$data_hora = $_POST['data_hora'] ?? '';

if ($id_filme == 0 || $sala == 0 || $formato === '' || $modo_exibicao === '' || $data_hora === '') {
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/cadastrar-horario.php?erro=campos-vazios");
    exit;
}

$dataConvertida = DateTime::createFromFormat('Y-m-d\TH:i', $data_hora);
if (!$dataConvertida) {
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/cadastrar-horario.php?erro=data-invalida");
    exit;
}

if ($id == 0) {

    $repositorio->salvar(
        $id_filme,
        intval($sala),
        FormatoFilme::from($formato),
        $modo_exibicao,
        $dataConvertida
    );

} else {
    $horario = new HorarioExibicaoFilme(
        $id_filme,
        $dataConvertida,
        intval($sala),
        FormatoFilme::from($formato),
        $modo_exibicao
    );

    $repositorio->atualizar($horario);
}

header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/horarios-filme-dashboard.php");
exit;
