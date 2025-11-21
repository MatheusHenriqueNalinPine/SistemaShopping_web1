<?php

//Referencias:
//Converter array php para javascript https://pt.stackoverflow.com/questions/382826/como-passar-array-php-para-javascript
//(primeira resposta)

use model\repositorio\AnuncioRepositorio;

require_once __DIR__ . "/../../../controller/conexao-bd.php";
require_once(__DIR__ . '/../../../model/repositorio/AnuncioRepositorio.php');

$repositorio = new AnuncioRepositorio($pdo);
$anuncios = $repositorio->buscarAnunciosCarrossel();
$anuncio = $anuncios[0] ?? null;


$anunciosJs = [];

$i = 0;

foreach ($anuncios as $anuncio) {
    $anunciosJs[$i] = [
            'id' => $anuncio->getId(),
            'nome' => $anuncio->getNome(),
            'descricao' => $anuncio->getDescricao(),
            'img' => $anuncio->getNomeImagem() ? '/SistemaShopping_web1/img/lojas/' . ltrim($anuncio->getNomeImagem(), '/')
                    : ($anuncio->getImagem()
                            ? 'data:' . $anuncio->getTipoImagem() . ';base64,' . $anuncio->getImagem()
                            : null)
    ];
    $i++;
}

$json = json_encode($anunciosJs);
?>

<a href="/SistemaShopping_web1/src/view/sessoes/anuncios/novidades.php?id=<?php echo htmlspecialchars($anuncio->getId()) ?>"
   id="anuncio-a">
    <div class="slide-ativo">
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
            <img src="<?php echo $imgSrc ?>"
                 alt="Imagem da loja <?php echo htmlspecialchars($anuncio->getNome()) ?>"
                 id="anuncio-img">
        <?php else: ?>
            <div class="placeholder">Sem imagem</div>
        <?php endif; ?>
        <div class="texto">
            <h2 id="titulo-anuncio"><?php echo htmlspecialchars($anuncio->getNome()) ?></h2>
            <p id="desc-anuncio"><?php echo htmlspecialchars($anuncio->getDescricao()) ?></p>
        </div>
    </div>
</a>

<script>
    let anuncios = <?= $json; ?>;
    let i = 0;

    const img = document.getElementById("anuncio-img");
    const titulo = document.getElementById("titulo-anuncio");
    const desc = document.getElementById("desc-anuncio");
    const link = document.getElementById("anuncio-a");

    function atualizarSlide() {
        let anuncio = anuncios[i];

        img.src = anuncio.img;
        titulo.innerText = anuncio.nome;
        desc.innerText = anuncio.descricao;
        link.href = "/SistemaShopping_web1/src/view/sessoes/anuncios/novidades.php?id=" + anuncio.id;

        if (i < anuncios.length - 1) {
            i++;
        } else {
            i = 0;
        }
    }

    atualizarSlide();
    setInterval(atualizarSlide, 3000);
</script>
