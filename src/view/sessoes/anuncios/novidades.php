<?php

use model\repositorio\AnuncioRepositorio;

require_once __DIR__ . '/../../../model/repositorio/AnuncioRepositorio.php';
require_once __DIR__ . '/../../../controller/conexao-bd.php';

$repositorio = new AnuncioRepositorio($pdo);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$anuncio = $id > 0 ? $repositorio->buscarPorId($id) : null;
$todasAnuncios = $repositorio->buscarTodos();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anuncios - SchweizerPine Shopping</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/index.css">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/anuncios.css">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/logoShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/telaDeAnuncio.css">
</head>
<body>

<?php include(__DIR__ . '/../header.html') ?>

<main>
    <div class="header-container">
        <a href="/SistemaShopping_web1/index.php" class="btn-voltar">← Voltar para Início</a>
        <h1 class="titulo-principal">Anúncios</h1>
        <div class="espacador"></div>
    </div>

    <?php if (!$anuncio && empty($todasAnuncios)) : ?>
        <div class="sem-dados">
            Nenhum anúncio cadastrado ainda.
        </div>
    <?php elseif ($anuncio) : ?>
        <div class="anuncio-card">
            <?php
            $imgSrc = '';
            $nomeArquivo = $anuncio->getNomeImagem();
            $tipo = $anuncio->getTipoImagem() ?? 'image/png';
            $imgBase64 = $anuncio->getImagem() ?? '';

            if (!empty($nomeArquivo)) {
                $imgSrc = '/SistemaShopping_web1/img/anuncios/' . ltrim($nomeArquivo, '/');
            } elseif (!empty($imgBase64)) {
                $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
            }
            ?>

            <?php if ($imgSrc !== ''): ?>
                <img src="<?php echo $imgSrc ?>"
                     alt="Imagem da anuncio <?php echo htmlspecialchars($anuncio->getNome()) ?>">
            <?php else: ?>
                <div class="placeholder">Sem imagem</div>
            <?php endif; ?>
            <div class="anuncio-info">
                <h1><?php echo htmlspecialchars($anuncio->getNome()) ?></h1>
                <p class="descricao"><?php echo nl2br(htmlspecialchars($anuncio->getDescricao())) ?></p>

                <div class="meta">
                    <p>
                        <strong>Categoria:</strong> <?php echo htmlspecialchars($repositorio->getCategoriaById($anuncio->getCategoriaAnuncio())) ?>
                    </p>
                    <p>
                        <strong>Formato:</strong> <?php echo htmlspecialchars($anuncio->getFormatoAnuncio()->value ?? '-') ?>
                    </p>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="anuncios-grid">
            <?php foreach ($todasAnuncios as $anuncio) : ?>
                <a href="?id=<?php echo $anuncio->getId() ?>" class="anuncio-card-small">
                    <?php
                    $imgSrc = '';
                    $nomeArquivo = $anuncio->getNomeImagem();
                    $tipo = $anuncio->getTipoImagem() ?? 'image/*';
                    $imgBase64 = $anuncio->getImagem() ?? '';

                    if (!empty($nomeArquivo)) {
                        $imgSrc = '/SistemaShopping_web1/img/anuncios/' . ltrim($nomeArquivo, '/');
                    } elseif (!empty($imgBase64)) {
                        $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
                    }
                    ?>

                    <div class="img-container">
                        <?php if ($imgSrc !== ''): ?>
                            <img src="<?php echo $imgSrc ?>"
                                 alt="Imagem da anuncio <?php echo htmlspecialchars($anuncio->getNome()) ?>">
                        <?php else: ?>
                            <div class="placeholder">Sem imagem</div>
                        <?php endif; ?>
                    </div>
                    <h2><?php echo htmlspecialchars($anuncio->getNome()) ?></h2>
                    <p class="descricao"><?php echo nl2br(htmlspecialchars($anuncio->getDescricao())) ?></p>
                    <div class="meta">
                        <p>
                            <strong>Categoria:</strong> <?php echo htmlspecialchars($repositorio->getCategoriaById($anuncio->getCategoriaAnuncio())) ?>
                        </p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php include(__DIR__ . '/../footer.html'); ?>
</body>
</html>
