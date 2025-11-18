<?php

use model\repositorio\AnuncioRepositorio;

require_once __DIR__ . "/../../../controller/conexao-bd.php";
require_once(__DIR__ . '/../../../model/repositorio/AnuncioRepositorio.php');
$limite = $limite_anuncios;

$repositorio = new AnuncioRepositorio($pdo);
$anuncios = $repositorio->buscarAnunciosHorizontais($limite);
?>

<?php foreach ($anuncios as $anuncio): ?>
    <div class="anuncio">
        <form action="/SistemaShopping_web1/src/view/sessoes/anuncios/novidades.php" method="get">
            <input type="hidden" name="id" value="<?php echo $anuncio->getId() ?>">
            <?php
            $imgSrc = '';
            $nomeArquivo = $anuncio->getNomeImagem();
            $tipo = $anuncio->getTipoImagem() ?? 'image/*';
            $imgBase64 = $anuncio->getImagem() ?? '';

            if (!empty($nomeArquivo)) {
                $imgSrc = '/SistemaShopping_web1/img/lojas/' . ltrim($nomeArquivo, '/');
            } elseif (!empty($imgBase64)) {
                $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
            }
            ?>
            <?php if ($imgSrc !== ''): ?>
                <button type="submit" class="img-button">
                    <img src="<?php echo $imgSrc ?>"
                         alt="Imagem da loja <?php echo htmlspecialchars($anuncio->getNome()) ?>">
                </button>
            <?php else: ?>
                <div class="placeholder">Sem imagem</div>
            <?php endif; ?>
        </form>
    </div>
<?php endforeach; ?>
