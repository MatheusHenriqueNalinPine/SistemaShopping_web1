<?php

use model\repositorio\AnuncioRepositorio;

require_once __DIR__ . "/../../../controller/conexao-bd.php";
require_once(__DIR__ . '/../../../model/repositorio/AnuncioRepositorio.php');

$repositorio = new AnuncioRepositorio($pdo);
$anuncios = $repositorio->buscarAnunciosCarrossel();
$anuncioAtual = $anuncios[0] ?? null;
?>

<form action="/SistemaShopping_web1/src/view/sessoes/anuncios/novidades.php" method="get">
    <div class="slide-ativo">
        <input type="hidden" name="id" value="<?php echo $anuncioAtual->getId() ?>">
        <?php
        $imgSrc = '';
        $nomeArquivo = $anuncioAtual->getNomeImagem();
        $tipo = $anuncioAtual->getTipoImagem() ?? 'image/*';
        $imgBase64 = $anuncioAtual->getImagem() ?? '';

        if (!empty($nomeArquivo)) {
            $imgSrc = '/SistemaShopping_web1/img/lojas/' . ltrim($nomeArquivo, '/');
        } elseif (!empty($imgBase64)) {
            $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
        }
        ?>
        <?php if ($imgSrc !== ''): ?>
            <button type="submit" class="img-button">
                <img src="<?php echo $imgSrc ?>"
                     alt="Imagem da loja <?php echo htmlspecialchars($anuncioAtual->getNome()) ?>">
            </button>
        <?php else: ?>
            <div class="placeholder">Sem imagem</div>
        <?php endif; ?>
        <div class="texto">
            <h2><?php echo htmlspecialchars($anuncioAtual->getNome()) ?></h2>
            <p><?php echo htmlspecialchars($anuncioAtual->getDescricao()) ?></p>
        </div>
    </div>
</form>
