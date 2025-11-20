<?php

use model\repositorio\AnuncioRepositorio;

require_once __DIR__ . '/../../../model/repositorio/AnuncioRepositorio.php';
require_once __DIR__ . '/../../../controller/conexao-bd.php';

$repositorio = new AnuncioRepositorio($pdo);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$filme = $id > 0 ? $repositorio->buscarPorId($id) : null;
$todasFilmes = $repositorio->buscarTodos();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cinema - SchweizerPine Shopping</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/index.css">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/logoShopping.png">
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
        <div class="filme-card">
            <?php
            $imgSrc = '';
            $nomeArquivo = $filme->getNomeImagem();
            $tipo = $filme->getTipoImagem() ?? 'image/png';
            $imgBase64 = $filme->getImagem() ?? '';

            if (!empty($nomeArquivo)) {
                $imgSrc = '/SistemaShopping_web1/img/filmes/' . ltrim($nomeArquivo, '/');
            } elseif (!empty($imgBase64)) {
                $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
            }
            ?>

            <?php if ($imgSrc !== ''): ?>
                <img src="<?php echo $imgSrc ?>"
                     alt="Imagem da filme <?php echo htmlspecialchars($filme->getNome()) ?>">
            <?php else: ?>
                <div class="placeholder">Sem imagem</div>
            <?php endif; ?>
            <div class="filme-info">
                <h1><?php echo htmlspecialchars($filme->getNome()) ?></h1>
                <p class="descricao">
                    <strong>Sinopse:</strong><br/><?php echo nl2br(htmlspecialchars($filme->getDescricao())) ?></p>

                <div class="meta">
                    <p>
                        <strong>Gênero:</strong> <?php echo htmlspecialchars($repositorio->getCategoriaById($filme->getCategoriaAnuncio())) ?>
                    </p>
                </div>
                <p><strong>Horários:</strong></p> <br/>

                <div class="horarios-filme-grid">
                    <?php foreach ($todasFilmes as $horarioExibicao) : ?>
                        <div class="horario-filme">
                            <p>
                                <strong>Data: </strong><?php echo htmlspecialchars($horarioExibicao->getDataRegistro()->format('d/m/Y')) ?>
                            </p>
                            <p class="horario"><?php echo htmlspecialchars($horarioExibicao->getDataRegistro()->format('H:i')) ?></p>
                            <p><strong>Sala: </strong><?php echo htmlspecialchars($horarioExibicao->getId()) ?></p>
                            <p class="tipo-filme"><?php echo htmlspecialchars('DUB')?></p>
                            <p class="tipo-filme"><?php echo htmlspecialchars('IMAX')?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="filmes-grid">
            <?php foreach ($todasFilmes as $filme) : ?>
                <a href="?id=<?php echo $filme->getId() ?>" class="filme-card-small">
                    <?php
                    $imgSrc = '';
                    $nomeArquivo = $filme->getNomeImagem();
                    $tipo = $filme->getTipoImagem() ?? 'image/*';
                    $imgBase64 = $filme->getImagem() ?? '';

                    if (!empty($nomeArquivo)) {
                        $imgSrc = '/SistemaShopping_web1/img/filmes/' . ltrim($nomeArquivo, '/');
                    } elseif (!empty($imgBase64)) {
                        $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
                    }
                    ?>

                    <div class="img-container">
                        <?php if ($imgSrc !== ''): ?>
                            <img src="<?php echo $imgSrc ?>"
                                 alt="Imagem do filme <?php echo htmlspecialchars($filme->getNome()) ?>">
                        <?php else: ?>
                            <div class="placeholder">Sem imagem</div>
                        <?php endif; ?>
                    </div>
                    <br>
                    <h2><?php echo htmlspecialchars($filme->getNome()) ?></h2>
                    <div class="meta">
                        <p>
                            <strong>Gênero:</strong> <?php echo htmlspecialchars($repositorio->getCategoriaById($filme->getCategoriaAnuncio())) ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <section class="anuncios-horizontais">
        <?php $limite_anuncios = 2;
        include('../../sessoes/anuncios/anuncio_horizontal.php');
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $filme = $id > 0 ? $repositorio->buscarPorId($id) : null; ?>
    </section>
</main>
<?php include(__DIR__ . '/../footer.html'); ?>
</body>
</html>
