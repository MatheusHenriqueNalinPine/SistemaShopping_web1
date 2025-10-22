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
        <img src="/SistemaShopping_web1/img/noticias.png" alt="Notícia">
        <h3><?php echo htmlspecialchars($anuncio->getNome()) ?></h3>
        <p><?php echo htmlspecialchars($anuncio->getDescricao()) ?></p>
        <span><?php echo htmlspecialchars($anuncio->getDataRegistro()->format('d/m/Y')) ?> - <?php echo htmlspecialchars($anuncio->getCategoriaAnuncio()) ?></span>
    </div>
<?php endforeach; ?>
