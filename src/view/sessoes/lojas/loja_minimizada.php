<?php

use model\repositorio\LojaRepositorio;

require_once __DIR__ . "/../../../controller/conexao-bd.php";
require_once(__DIR__ . '/../../../model/repositorio/LojaRepositorio.php');
$limite = $limite_lojas;

$repositorio = new LojaRepositorio($pdo);
$lojas = $repositorio->buscarLojasMinimizadas($limite);
?>

<?php foreach ($lojas as $loja): ?>
    <div class="logo-box-login">
        <a href="/SistemaShopping_web1/src/view/sessoes/lojas/telaDeLoja.php?id=<?php echo htmlspecialchars($loja->getId()) ?>"><img
                    src="<?php echo htmlspecialchars($loja->getUrlImagem()) ?>"
                    alt="Logo da loja <?php echo htmlspecialchars($loja->getNome()) ?>"></a>
    </div>
<?php endforeach; ?>
