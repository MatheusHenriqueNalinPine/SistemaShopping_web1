<?php

use model\repositorio\AnuncioRepositorio;

require_once __DIR__ . "/../../../controller/conexao-bd.php";
require_once(__DIR__ . '/../../../model/repositorio/AnuncioRepositorio.php');

$repositorio = new AnuncioRepositorio($pdo);
$anuncios = $repositorio->buscarAnunciosCarrossel();
$anuncioAtual = $anuncios[0] ?? null;
$anunciosJs = array_map(function ($a) {
    return [
            "id" => $a->getId(),
            "nome" => $a->getNome(),
            "descricao" => $a->getDescricao(),
            "img" => $a->getNomeImagem()
                    ? '/SistemaShopping_web1/img/lojas/' . ltrim($a->getNomeImagem(), '/')
                    : ($a->getImagem()
                            ? 'data:' . $a->getTipoImagem() . ';base64,' . $a->getImagem()
                            : null)
    ];
}, $anuncios);
?>

<a href="/SistemaShopping_web1/src/view/sessoes/anuncios/novidades.php?<?php echo htmlspecialchars($anuncioAtual->getId()) ?>)"
   id="anuncio-a">
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
            <img src="<?php echo $imgSrc ?>"
                 alt="Imagem da loja <?php echo htmlspecialchars($anuncioAtual->getNome()) ?>"
                 id="anuncio-img">
        <?php else: ?>
            <div class="placeholder">Sem imagem</div>
        <?php endif; ?>
        <div class="texto">
            <h2 id="titulo-anuncio"><?php echo htmlspecialchars($anuncioAtual->getNome()) ?></h2>
            <p id="desc-anuncio"><?php echo htmlspecialchars($anuncioAtual->getDescricao()) ?></p>
        </div>
    </div>
</a>

<script>
    let anuncios = <?= json_encode($anunciosJs); ?>;
    let i = 0;

    const img = document.getElementById("anuncio-img");
    const titulo = document.getElementById("titulo-anuncio");
    const desc = document.getElementById("desc-anuncio");
    const link = document.getElementById("anuncio-a");

    function atualizarSlide() {
        let anuncio = anuncios[i];

        setTimeout(() => {
            img.src = anuncio.img;
            titulo.innerText = anuncio.nome;
            desc.innerText = anuncio.descricao;
            link.href = "/SistemaShopping_web1/src/view/sessoes/anuncios/novidades.php?" + anuncio.id;

            if (i < anuncios.length - 1) {
                i++;
            } else {
                i = 0;
            }
        }, 300);
    }

    atualizarSlide();
    setInterval(atualizarSlide, 3000);
</script>
