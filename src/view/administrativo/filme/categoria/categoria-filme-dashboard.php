<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../../../model/repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../../../../controller/conexao-bd.php';

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;
if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);
$cargo = $usuario->getCargo();
if ($cargo == Cargo::Funcionario_cinema || $cargo == Cargo::Gerenciador_anuncio) {
    header('Location: /SistemaShopping_web1/src/view/administrativo/administrativo.php');
    exit;
}

// tenta carregar repositório de categorias de filme (se existir)
$categorias = [];
$categoriaRepoFile = __DIR__ . '/../../../../model/repositorio/CategoriaFilmeRepositorio.php';
if (file_exists($categoriaRepoFile)) {
    require_once $categoriaRepoFile;
    if (class_exists('\model\repositorio\CategoriaFilmeRepositorio')) {
        try {
            $categorias = (new \model\repositorio\CategoriaFilmeRepositorio($pdo))->buscarTodas();
        } catch (Throwable $e) {
            error_log("Erro ao buscar categorias de filme: " . $e->getMessage());
            $categorias = [];
        }
    } else {
        error_log("Classe CategoriaFilmeRepositorio não encontrada em: $categoriaRepoFile");
    }
} else {
    error_log("CategoriaFilmeRepositorio.php não encontrado em: $categoriaRepoFile");
}

// Fallback direto: se não obteve categorias via repositório, buscar diretamente na tabela
if (empty($categorias)) {
    try {
        $stmt = $pdo->query("SELECT id, categoria FROM tbcategoriafilme ORDER BY categoria");
        $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (is_array($fetched) && count($fetched) > 0) {
            $categorias = $fetched;
        }
    } catch (Throwable $e) {
        error_log("Falha ao carregar categorias de filme via PDO: " . $e->getMessage());
        // mantém $categorias como array vazio
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Categorias Filme - Administrativo</title>
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/crud-tabela.css">
</head>
<body>

<?php include('../../menu.php') ?>
<?php include('../../sidebar.php') ?>

<main class="conteudo">
    <h2>Gerenciamento de Categorias (filme)</h2>

    <div class="acoes">
        <a href="cadastrar-categoria.php" class="btn-cadastrar">Cadastrar categoria</a>
    </div>

    <table class="tabela">
        <thead>
        <tr>
            <th>Id</th>
            <th>Categoria</th>
            <th>Remover</th>
            <th>Editar</th>
        </tr>
        </thead>
        <tbody>
        <?php if (count($categorias) == 0) : ?>
            <tr>
                <td colspan="4" class="sem-dados">Nenhuma categoria cadastrada</td>
            </tr>
        <?php else: ?>
            <?php foreach ($categorias as $categoria) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($categoria['id']) ?></td>
                    <td><?php echo htmlspecialchars($categoria['categoria']) ?></td>
                    <td>
                        <form action="/SistemaShopping_web1/src/controller/exclusao/excluir-categoria-filme.php" method="post">
                            <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
                            <input type="submit" class="botao-excluir" value="Excluir">
                        </form>
                    </td>
                    <td>
                        <form action="editar-categoria.php" method="get">
                            <input type="hidden" name="categoria" value="<?= htmlspecialchars($categoria['categoria']) ?>">
                            <input type="hidden" name="id" value="<?= $categoria['id'] ?>">
                            <input type="submit" class="btn-editar" value="Editar">
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</main>
</body>
</html>
