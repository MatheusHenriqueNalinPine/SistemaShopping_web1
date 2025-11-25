<?php

require_once __DIR__ . "/../conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;
if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/sessoes/login.php");
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    header("Location: /SistemaShopping_web1/src/view/administrativo/loja/horarios/horarios-loja-dashboard.php");
    exit;
}


$repoFile = __DIR__ . '/../../model/repositorio/CategoriaLojaRepositorio.php';
if (file_exists($repoFile)) {
    require_once $repoFile;
    if (class_exists('\model\repositorio\CategoriaLojaRepositorio')) {
        $repositorio = new \model\repositorio\CategoriaLojaRepositorio($pdo);
        if (method_exists($repositorio, 'isCategoriaUsed') && $repositorio->isCategoriaUsed($id)) {
            header("Location: /SistemaShopping_web1/src/view/administrativo/loja/horarios/horarios-loja-dashboard.php?erro=usada&erro_id=" . urlencode($id));
            exit;
        }
        try {
            if (method_exists($repositorio, 'excluir')) {
                $repositorio->excluir($id);
                header("Location: /SistemaShopping_web1/src/view/administrativo/loja/horarios/horarios-loja-dashboard.php");
                exit;
            }
        } catch (Throwable $e) {
            error_log("Falha ao excluir horarios via repositório: " . $e->getMessage());
            header("Location: /SistemaShopping_web1/src/view/administrativo/loja/horarios/horarios-loja-dashboard.php?erro=usada&erro_id=" . urlencode($id));
            exit;
        }
    }
}


try {
    $del = $pdo->prepare("DELETE FROM tbcategorialoja WHERE id = ?;");
    $del->execute([$id]);
    header("Location: /SistemaShopping_web1/src/view/administrativo/loja/horarios/horarios-loja-dashboard.php");
    exit;
} catch (PDOException $e) {
    
    if (isset($e->errorInfo[0]) && $e->errorInfo[0] === '23000') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/loja/horarios/horarios-loja-dashboard.php?erro=usada&erro_id=" . urlencode($id));
        exit;
    }
    error_log("Erro ao excluir horarios-loja (PDO): " . $e->getMessage());
    header("Location: /SistemaShopping_web1/src/view/administrativo/loja/horarios/horarios-loja-dashboard.php?erro=sql");
    exit;
}