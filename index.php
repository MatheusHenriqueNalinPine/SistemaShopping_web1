<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/index.css">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
</head>

<body>

<?php include('src/view/sessoes/header.html') ?>

<section class="carrossel">
    <?php include('src/view/sessoes/anuncios/anuncio_carrossel.php') ?>
</section>


<section class="anuncios-horizontais">
    <?php $limite_anuncios = 2;
    include('src/view/sessoes/anuncios/anuncio_horizontal.php') ?>
</section>

<section class="noticias">
    <div class="cabecalho">
        <h2>Notícias</h2>
        <a href="src/view/sessoes/anuncios/novidades.php">VER MAIS</a>
    </div>
    <div class="grid-noticias">
        <?php $limite_anuncios = 4;
        include('src/view/sessoes/anuncios/anuncio_minimizado.php') ?>
    </div>
</section>

<section class="lojas">
    <h2>Algumas de nossas lojas</h2>
    <div class="carrossel-lojas">
        <div class="logos">
            <?php $limite_lojas = 5;
            include('src/view/sessoes/lojas/loja_minimizada.php'); ?>
        </div>
    </div>
    <a href="src/view/sessoes/lojas/telaDeLoja.php" class="ver-mais">VER MAIS</a>
</section>


<section class="entretenimento">
    <h2>Entretenimento</h2>
    <div class="bloco">
        <img src="/SistemaShopping_web1/img/cinema.jpg" alt="Cinema">
        <div class="texto">
            <h3>Cinema</h3>
            <p>Viva a emoção das telonas com o que há de melhor em imagem e som.
                Nosso cinema conta com salas modernas, tecnologia 3D, poltronas reclináveis e uma programação
                completa...</p>
        </div>
    </div>
    <div class="bloco">
        <div class="texto">
            <h3>Teatro</h3>
            <p>Espetáculos que encantam todas as idades. O teatro do shopping oferece uma programação cultural
                diversificada...</p>
        </div>
        <img src="/SistemaShopping_web1/img/teatro.webp" alt="Teatro">
    </div>
</section>

<?php include('./src/view/sessoes/footer.html') ?>

</body>
</html>