<?php

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php?erro=metodo");
    exit;
}

$id = $_POST['id'] ?? null;
if (empty($id)) {
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php?erro=sem_id");
    exit;
}


$srcDir = dirname(__DIR__, 2); 
$repoPath = $srcDir . '/model/repositorio/FilmeRepositorio.php';
$filmePath = $srcDir . '/model/servico/filme/Filme.php';
$conexaoPath = $srcDir . '/controller/conexao-bd.php';

if (!file_exists($conexaoPath)) {
    error_log("Arquivo de conexão não encontrado em: $conexaoPath");
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php?erro=conexao");
    exit;
}
require_once $conexaoPath;

if (file_exists($repoPath)) {
    require_once $repoPath;
} else {
    error_log("FilmeRepositorio.php não encontrado em: $repoPath — será usado fallback PDO para exclusão.");
}


if (file_exists($filmePath)) {
    error_log("Filme.php encontrado em $filmePath — não será incluído aqui para evitar dependências faltantes.");
} else {
    error_log("Filme.php não encontrado em: $filmePath");
}

$repositorio = null;
$sucesso = false;

try {
    // tenta instanciar repositório se disponível
    if (class_exists('\model\repositorio\FilmeRepositorio')) {
        $repositorio = new \model\repositorio\FilmeRepositorio($pdo);
    } elseif (class_exists('FilmeRepositorio')) {
        $repositorio = new FilmeRepositorio($pdo);
    }

    if ($repositorio) {
        // tenta métodos comuns em ordem
        if (method_exists($repositorio, 'excluir')) {
            $sucesso = (bool)$repositorio->excluir($id);
        } elseif (method_exists($repositorio, 'excluirFilme')) {
            $sucesso = (bool)$repositorio->excluirFilme($id);
        } elseif (method_exists($repositorio, 'delete') ) {
            $sucesso = (bool)$repositorio->delete($id);
        } elseif (method_exists($repositorio, 'deletar') ) {
            $sucesso = (bool)$repositorio->deletar($id);
        } elseif (method_exists($repositorio, 'deleteById') ) {
            $sucesso = (bool)$repositorio->deleteById($id);
        } else {
            // nenhum método conhecido: tentar fallback PDO abaixo
            $repositorio = null;
        }
    }

    // fallback direto via PDO se repositório não forneceu exclusão
    if (!$sucesso && !$repositorio) {
        $stmt = $pdo->prepare("DELETE FROM tbfilme WHERE id = :id");
        $sucesso = $stmt->execute([':id' => $id]);
    }
} catch (Throwable $e) {
    error_log("Erro ao excluir filme id={$id}: " . $e->getMessage());
    $sucesso = false;
}

if ($sucesso) {
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php?sucesso=1");
} else {
    header("Location: /SistemaShopping_web1/src/view/administrativo/filme/filme-dashboard.php?erro=sql");
}
exit;
