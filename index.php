<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/index.css">
</head>

<body>

<?php include ('src/view/sessoes/header.html')?>

<section class="carrossel">
    <div class="slide ativo">
        <img src="/SistemaShopping_web1/img/carrosel.jpg" alt="Anúncio 1">
        <div class="texto">
            <h2>Anúncio</h2>
            <p>Venha conhecer nosso carrosel 🎠</p>
            <a href="#" class="btn">Conheça nossas lojas!</a>
        </div>
    </div>
    <div class="slide">
        <img src="/SistemaShopping_web1/img/kart.jpeg" alt="Anúncio 2">
        <div class="texto">
            <h2>Anúncio</h2>
            <p>Venha acelerar seu coração com o nosso kart 🏎️</p>
            <a href="#" class="btn">Ver mais</a>
        </div>
    </div>
    <div class="indicadores">
        <span class="ativo"></span>
        <span></span>
    </div>
</section>


<section class="anuncios-horizontais">
    <div class="anuncio">
        <h3>Anúncio</h3>
        <p>Esse é um exemplo de anúncio horizontal</p>
        <p>Usado para eventos ou atrações fixas</p>
    </div>
    <div class="anuncio">
        <h3>Anúncio</h3>
        <p>Esse é um exemplo de anúncio horizontal</p>
        <p>Usado para eventos ou atrações fixas</p>
    </div>
</section>


<section class="noticias">
    <div class="cabecalho">
        <h2>Notícias</h2>
        <a href="#">VER MAIS</a>
    </div>
    <div class="grid-noticias">
        <div class="noticia">
            <img src="/SistemaShopping_web1/img/noticias.png" alt="Notícia">
            <h3>Título anúncio</h3>
            <p>Detalhes do anúncio</p>
            <span>dd/MM/yyyy - Assunto</span>
        </div>
        <div class="noticia">
            <img src="/SistemaShopping_web1/img/noticias.png" alt="Notícia">
            <h3>Título anúncio</h3>
            <p>Detalhes do anúncio</p>
            <span>dd/MM/yyyy - Assunto</span>
        </div>
        <div class="noticia">
            <img src="/SistemaShopping_web1/img/noticias.png" alt="Notícia">
            <h3>Título anúncio</h3>
            <p>Detalhes do anúncio</p>
            <span>dd/MM/yyyy - Assunto</span>
        </div>
        <div class="noticia">
            <img src="/SistemaShopping_web1/img/noticias.png" alt="Notícia">
            <h3>Título anúncio</h3>
            <p>Detalhes do anúncio</p>
            <span>dd/MM/yyyy - Assunto</span>
        </div>
    </div>
</section>

<section class="lojas">
    <h2>Algumas de nossas lojas</h2>
    <div class="carrossel-lojas">
        <span class="seta">❮</span>

        <div class="logos">
            <div class="logo-box">Logo 1</div>
            <div class="logo-box">Logo 2</div>
            <div class="logo-box">Logo 3</div>
        </div>

        <span class="seta">❯</span>
    </div>
    <a href="#" class="ver-mais">VER MAIS</a>
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

<?php include ('./src/view/sessoes/footer.html')?>

</body>
</html>