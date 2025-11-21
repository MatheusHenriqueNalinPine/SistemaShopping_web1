<?php

use model\repositorio\AnuncioRepositorio;

require_once __DIR__ . "/../../../controller/conexao-bd.php";
require_once(__DIR__ . '/../../../model/repositorio/AnuncioRepositorio.php');
$limite = $limite_anuncios;

$repositorio = new AnuncioRepositorio($pdo);
$anuncios = $repositorio->buscarAnunciosMinimizados($limite);
?>

<?php foreach ($anuncios as $anuncio): ?>
    <div class="noticia">
        <a href="/SistemaShopping_web1/src/view/sessoes/anuncios/novidades.php?id=<?php echo $anuncio->getId() ?>"
           class="anuncio-card-small">
            <?php
            $imgSrc = '';
            $nomeArquivo = $anuncio->getNomeImagem();
            $tipoImagem = $anuncio->getTipoImagem() ?? 'image/*';

            if (!empty($nomeArquivo)) {
                $imgSrc = '/SistemaShopping_web1/img/lojas/' . ltrim($nomeArquivo, '/');
            } elseif (!empty($anuncio->getImagem())) {
                $imgSrc = 'data:' . $tipoImagem . ';base64,' . $anuncio->getImagem();
            }

            if (strpos($imgSrc, 'data:') !== false && !empty($anuncio->getImagem())) {
                $imgSrc = 'data:' . $tipoImagem . ';base64,' . $anuncio->getImagem();
            }
            ?>
            <?php if (!empty($imgSrc)): ?>
            <img src="<?php echo $imgSrc ?>"
                 alt="Imagem da loja <?php echo htmlspecialchars($anuncio->getNome()) ?>">
            <?php else: ?>
            <div class="placeholder">Sem imagem</div>
            <?php endif; ?>
            <h3><?php echo htmlspecialchars($anuncio->getNome()) ?></h3>
            <p><?php echo htmlspecialchars($anuncio->getDescricao()) ?></p>
            <span><?php echo htmlspecialchars($anuncio->getDataRegistro()->format('d/m/Y')) ?> - <?php echo htmlspecialchars($anuncio->getCategoriaAnuncio()) ?></span>
        </a>
    </div>
<?php endforeach; ?>
