<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use model\repositorio\CategoriaFilmeRepositorio;

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
$categoria = trim($_POST['horarios'] ?? '');

if ($id == 0) {
    if ($categoria === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/cadastrar-horarios.php?erro=campos-vazios");
        exit;
    }

    // tenta usar repositório se existir
    $repoFile = __DIR__ . '/../../model/repositorio/CategoriaFilmeRepositorio.php';
    if (file_exists($repoFile)) {
        require_once $repoFile;
        if (class_exists('\model\repositorio\CategoriaFilmeRepositorio')) {
            $repositorio = new \model\repositorio\CategoriaFilmeRepositorio($pdo);
            $repositorio->salvar($categoria);
            header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/horarios-filme-dashboard.php");
            exit;
        }
    }

    // fallback direto com PDO
    $sql = "INSERT INTO tbcategoriafilme (horarios) VALUES (?);";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$categoria]);

    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/horarios-filme-dashboard.php");
    exit;

} else {
    if ($categoria === '') {
        header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/editar-horarios.php?erro=campos-vazios&id=" . urlencode($id) . "&horarios=" . urlencode($categoria));
        exit;
    }

    // tenta usar repositório se existir
    $repoFile = __DIR__ . '/../../model/repositorio/CategoriaFilmeRepositorio.php';
    if (file_exists($repoFile)) {
        require_once $repoFile;
        if (class_exists('\model\repositorio\CategoriaFilmeRepositorio')) {
            $repositorio = new \model\repositorio\CategoriaFilmeRepositorio($pdo);
            $repositorio->alterar($id, $categoria);
            header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/horarios-filme-dashboard.php?id=" . urlencode($id));
            exit;
        }
    }

    // fallback direto com PDO
    $sql = "UPDATE tbcategoriafilme SET horarios = ? WHERE id = ?;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$categoria, $id]);

    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/horarios-filme-dashboard.php?id=" . urlencode($id));
    exit;
}

header("Location: /SistemaShopping_web1/src/view/administrativo/filme/horarios/horarios-filme-dashboard.php");
exit;
