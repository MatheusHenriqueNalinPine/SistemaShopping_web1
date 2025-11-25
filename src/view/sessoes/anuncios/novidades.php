<?php

use model\repositorio\AnuncioRepositorio;

require_once __DIR__ . '/../../../model/repositorio/AnuncioRepositorio.php';
require_once __DIR__ . '/../../../controller/conexao-bd.php';

$repositorio = new AnuncioRepositorio($pdo);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$todasAnuncios = $repositorio->buscarTodos();


$selectedLimit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
$anunciosUltimos = [];
if ($selectedLimit > 0) {

    $anunciosUltimos = array_slice($todasAnuncios, 0, $selectedLimit);
}

$anuncio = $id > 0 ? $repositorio->buscarPorId($id) : null;
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anúncios - SchweizerPine Shopping</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/index.css">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/anuncios.css">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/telaDeAnuncio.css">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/telaDeAnuncios.css">
</head>
<body>

<?php include(__DIR__ . '/../header.html') ?>

<main>
    <div class="header-container">
        <a href="/SistemaShopping_web1/index.php" class="btn-voltar">← Voltar para Início</a>
        <h1 class="titulo-principal">Anúncios</h1>

        <div class="toolbar">
            <div class="ordenacao">
                <label for="limitSelect" class="label-compact">Ver</label>
                <select id="limitSelect" name="limit" class="select-compact"
                        onchange="(function(){
                            const limit = parseInt(this.value,10) || 0;
                            const params = new URLSearchParams(window.location.search);
                            if(limit > 0) {
                                params.set('limit', limit);
                            } else {
                                params.delete('limit');
                            }
                            params.delete('id');
                            window.location.href = window.location.pathname + (params.toString() ? ('?' + params.toString()) : '');
                        }).call(this);">
                    <option value="0" <?php echo $selectedLimit === 0 ? 'selected' : '' ?>>Padrão</option>
                    <option value="5" <?php echo $selectedLimit === 5 ? 'selected' : '' ?>>Últimos 5</option>
                    <option value="10" <?php echo $selectedLimit === 10 ? 'selected' : '' ?>>Últimos 10</option>
                    <option value="15" <?php echo $selectedLimit === 15 ? 'selected' : '' ?>>Últimos 15</option>
                </select>
            </div>
            <div class="info-pages">
                <?php if ($selectedLimit > 0): ?>
                    <span class="page-summary">Mostrando últimos <?php echo htmlspecialchars($selectedLimit); ?> anúncios</span>
                <?php endif; ?>
            </div>
        </div>
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
            $tipo = $anuncio->getTipoImagem() ?? 'image/*';
            $imgBase64 = $anuncio->getImagem() ?? '';

            if (!empty($nomeArquivo)) {
                $imgSrc = '/SistemaShopping_web1/img/anuncios/' . ltrim($nomeArquivo, '/');
            } elseif (!empty($imgBase64)) {
                $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
            }
            ?>
            <?php if ($imgSrc !== ''): ?>
                <img src="<?php echo $imgSrc ?>"
                     alt="Imagem do anúncio <?php echo htmlspecialchars($anuncio->getNome()) ?>"
                     id="anuncio-img">
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

        <?php if ($selectedLimit > 0): ?>
        <section class="ultimos-anuncios">
            <h2>Últimos <?php echo htmlspecialchars($selectedLimit); ?> anúncios</h2>
            <?php if (empty($anunciosUltimos)): ?>
                <p>Nenhum anúncio encontrado.</p>
            <?php else: ?>
                <div class="anuncios-list">
                    <?php foreach ($anunciosUltimos as $anuncio):
                        $imgSrc = '';
                        $nomeArquivo = $anuncio->getNomeImagem();
                        $tipo = $anuncio->getTipoImagem() ?? 'image/png';
                        $imgBase64 = $anuncio->getImagem() ?? '';
                        if (!empty($nomeArquivo)) {
                            $imgSrc = '/SistemaShopping_web1/img/anuncios/' . ltrim($nomeArquivo, '/');
                        } elseif (!empty($imgBase64)) {
                            $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
                        }
                        $anuncioId = $anuncio->getId();
                    ?>
                        <article class="anuncio-item">
                            <div class="anuncio-thumb">
                                <?php if ($imgSrc !== ''): ?>
                                    <img src="<?php echo $imgSrc ?>" alt="<?php echo htmlspecialchars($anuncio->getNome()) ?>">
                                <?php else: ?>
                                    <div class="thumb-placeholder">Sem imagem</div>
                                <?php endif; ?>
                            </div>
                            <div class="anuncio-body">
                                <h3><?php echo htmlspecialchars($anuncio->getNome()) ?></h3>
                                <p class="descricao"><?php echo nl2br(htmlspecialchars(substr($anuncio->getDescricao(), 0, 150))) ?></p>
                                <a class="link-detalhe" href="?id=<?php echo (int)$anuncioId ?>">Ver anúncio</a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>

        <?php else: ?>
           
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
                                     alt="Imagem do anúncio <?php echo htmlspecialchars($anuncio->getNome()) ?>">
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

    <?php endif; ?>
</main>
<?php include(__DIR__ . '/../footer.html'); ?>
</body>
</html>

