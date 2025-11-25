<?php

use model\repositorio\HorarioFilmeRepositorio;

require_once __DIR__ . '/../../../controller/conexao-bd.php';
require_once __DIR__ . '/../../../model/repositorio/CinemaRepositorio.php';
require_once __DIR__ . '/../../../model/repositorio/HorarioFilmeRepositorio.php';

$repositorio = new \model\repositorio\CinemaRepositorio($pdo);
$categoriaRepo = new \model\repositorio\HorarioFilmeRepositorio($pdo);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$filme = null;
$todasFilmes = [];

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
if ($id > 0) {
    $filme = $repositorio->buscarPorId($id);
    $todasFilmes = $repositorio->buscarTodos();
} else {
    if ($limit > 0) {
        $todasFilmes = $repositorio->buscarUltimos($limit);
    } else {
        $todasFilmes = $repositorio->buscarTodos();
    }
}

function get_val($item, $key, $default = '')
{
    if (is_array($item)) {
        return $item[$key] ?? $default;
    }
    if (is_object($item)) {
        $method = 'get' . str_replace(' ', '', ucwords(str_replace(['_', '-'], ' ', $key)));
        if (method_exists($item, $method)) return $item->$method();
        return $item->$key ?? $default;
    }
    return $default;
}

$horariosRepo = new HorarioFilmeRepositorio($pdo);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema - SchweizerPine Shopping</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/index.css">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/telaDeCinema.css">
</head>
<body>

<?php include(__DIR__ . '/../header.html') ?>

<main>
    <div class="header-container">
        <a href="/SistemaShopping_web1/index.php" class="btn-voltar">← Voltar para Início</a>
        <h1 class="titulo-principal">Filmes em cartaz</h1>
        <div class="espacador"></div>
    </div>


    <?php if (!$filme && empty($todasFilmes)) : ?>
        <div class="sem-dados">
            Nenhum filme cadastrado ainda.
        </div>

    <?php elseif ($filme) : ?>
        <?php
        $nomeArquivo = get_val($filme, 'nome_imagem', '') ?: get_val($filme, 'nomeImagem', '');
        $tipo = get_val($filme, 'tipo_imagem', '') ?: get_val($filme, 'tipoImagem', 'image/png');
        $imgBase64 = get_val($filme, 'imagem', '') ?: get_val($filme, 'imagem_base64', '');
        $nome = get_val($filme, 'nome', '') ?: get_val($filme, 'titulo', '');
        $descricao = get_val($filme, 'descricao', '');
        $categoria_nome = get_val($filme, 'categoria_nome', '');
        if (empty($categoria_nome)) {
            $catId = (int)(get_val($filme, 'id_categoria_filme', 0));
            if ($catId) $categoria_nome = $categoriaRepo->buscarPorId($catId) ?: $categoria_nome;
        }
        $sala = get_val($filme, 'sala', '') ?: get_val($filme, 'posicao', '');
        $formato = get_val($filme, 'formato', '');
        $horariosRaw = get_val($filme, 'horarios', '');
        $horarios = [];
        if (!empty($horariosRaw)) {
            $decoded = json_decode($horariosRaw, true);
            if (is_array($decoded)) $horarios = $decoded;
        }
        $imgSrc = '';
        if (!empty($nomeArquivo)) {
            $imgSrc = '/SistemaShopping_web1/img/filmes/' . ltrim($nomeArquivo, '/');
        } elseif (!empty($imgBase64)) {
            $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
        }
        ?>
        <div class="filme-card">
            <?php if ($imgSrc !== ''): ?>
                <img src="<?php echo $imgSrc ?>"
                     alt="Imagem do filme <?php echo htmlspecialchars($nome) ?>">
            <?php else: ?>
                <div class="placeholder">Sem imagem</div>
            <?php endif; ?>
            <div class="filme-info">
                <h1><?php echo htmlspecialchars($nome) ?></h1>
                <p class="descricao">
                    <strong>Sinopse:</strong><br/><?php echo nl2br(htmlspecialchars($descricao)) ?></p>

                <div class="meta">
                    <p><strong>Gênero:</strong> <?php echo htmlspecialchars($filme->getGenero()) ?></p>
                </div>

                <?php if (!empty($filme)): ?>
                    <p><strong>Horários:</strong></p>
                    <div class="horarios-filme-grid">
                        <?php foreach ($horariosRepo->buscarPorIdFilme($id) as $horario) : ?>
                            <?php
                            $dataHora = $horario->getDataHora();
                            $sala = $horario->getSala();
                            $formato = $horario->getFormatoFilme()->value ?? '';
                            $modo = $horario->getModoExibicao() ?? '';
                            ?>
                            <div class="horario-filme">
                                <p><strong>Data: </strong><?php echo htmlspecialchars($dataHora->format('d/m/Y')) ?></p>
                                <p class="horario"><?php echo htmlspecialchars($dataHora->format('H:i')) ?></p>
                                <p><strong>Sala: </strong><?php echo htmlspecialchars($sala) ?></p>
                                <p class="tipo-filme"><?php echo htmlspecialchars(strtoupper($formato)) ?></p>
                                <p class="tipo-filme"><?php echo htmlspecialchars(strtoupper($modo)) ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>

    <?php else : ?>
        <div class="filmes-grid">
            <?php foreach ($todasFilmes as $item) :
                $fid = get_val($item, 'id', get_val($item, 'Id', 0));
                $nome = get_val($item, 'nome', '');
                $nomeArquivo = get_val($item, 'nome_imagem', '') ?: get_val($item, 'nomeImagem', '');
                $tipo = get_val($item, 'tipo_imagem', '') ?: get_val($item, 'tipoImagem', 'image/*');
                $imgBase64 = get_val($item, 'imagem', '') ?: '';
                $imgSrc = '';
                if (!empty($nomeArquivo)) {
                    $imgSrc = '/SistemaShopping_web1/img/filmes/' . ltrim($nomeArquivo, '/');
                } elseif (!empty($imgBase64)) {
                    $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
                }
                $categoria_nome = get_val($item, 'categoria_nome', '');
                ?>
                <a href="?id=<?php echo (int)$fid ?>" class="filme-card-small">
                    <div class="img-container">
                        <?php if ($imgSrc !== ''): ?>
                            <img src="<?php echo $imgSrc ?>"
                                 alt="Imagem do filme <?php echo htmlspecialchars($nome) ?>">
                        <?php else: ?>
                            <div class="placeholder">Sem imagem</div>
                        <?php endif; ?>
                    </div>
                    <br>
                    <h2><?php echo htmlspecialchars($nome) ?></h2>
                    <div class="meta">
                        <p><strong>Categoria:</strong> <?php echo htmlspecialchars($categoria_nome) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <section class="anuncios-horizontais">
        <?php $limite_anuncios = 2;
        include('../../sessoes/anuncios/anuncio_horizontal.php');
        ?>
    </section>
</main>
<?php include(__DIR__ . '/../footer.html'); ?>
</body>
</html>
