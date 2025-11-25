<?php

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/anuncio-dashboard.php?erro=metodo");
    exit;
}

$id = $_POST['id'] ?? null;
if (empty($id)) {
    header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/anuncio-dashboard.php?erro=sem_id");
    exit;
}


$srcDir = dirname(__DIR__, 2); 
$repoPath = $srcDir . '/model/repositorio/AnuncioRepositorio.php';
$anuncioPath = $srcDir . '/model/servico/anuncio/Anuncio.php';
$conexaoPath = $srcDir . '/controller/conexao-bd.php';

if (!file_exists($conexaoPath)) {
    error_log("Arquivo de conexão não encontrado em: $conexaoPath");
    header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/anuncio-dashboard.php?erro=conexao");
    exit;
}
require_once $conexaoPath;


if (file_exists($repoPath)) {
    require_once $repoPath;
} else {
    error_log("AnuncioRepositorio.php não encontrado em: $repoPath — será usado fallback PDO para exclusão.");
}


if (file_exists($anuncioPath)) {
    error_log("Anuncio.php encontrado em $anuncioPath — não será incluído aqui para evitar dependências faltantes.");
} else {
    error_log("Anuncio.php não encontrado em: $anuncioPath");
}

$repositorio = null;
$sucesso = false;

try {
    if (class_exists('\model\repositorio\AnuncioRepositorio')) {
        $repositorio = new \model\repositorio\AnuncioRepositorio($pdo);
    } elseif (class_exists('AnuncioRepositorio')) {
        $repositorio = new AnuncioRepositorio($pdo);
    }

    if ($repositorio) {
        if (method_exists($repositorio, 'excluir')) {
            $sucesso = (bool)$repositorio->excluir($id);
        } elseif (method_exists($repositorio, 'excluirAnuncio')) {
            $sucesso = (bool)$repositorio->excluirAnuncio($id);
        } elseif (method_exists($repositorio, 'delete')) {
            $sucesso = (bool)$repositorio->delete($id);
        } elseif (method_exists($repositorio, 'deletar')) {
            $sucesso = (bool)$repositorio->deletar($id);
        } else {
            $repositorio = null; 
        }
    }

    if (!$sucesso && !$repositorio) {
        $stmt = $pdo->prepare("DELETE FROM tbanuncio WHERE id = :id");
        $sucesso = $stmt->execute([':id' => $id]);
    }
} catch (Throwable $e) {
    error_log("Erro ao excluir anuncio id={$id}: " . $e->getMessage());
    $sucesso = false;
}

if ($sucesso) {
    header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/anuncio-dashboard.php?sucesso=1");
} else {
    header("Location: /SistemaShopping_web1/src/view/administrativo/anuncio/anuncio-dashboard.php?erro=sql");
}
exit;