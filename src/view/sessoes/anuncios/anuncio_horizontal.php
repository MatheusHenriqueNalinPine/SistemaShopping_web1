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
        <form action="#" method="post">
            <input type="hidden" value="<?php echo $anuncio->getIdAnuncio() ?>">
            <input type="submit"> <img src="/SistemaShopping_web1/img/noticias.png" alt="Anúncio">
        </form>
    </div>
<?php endforeach; ?>
