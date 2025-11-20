
<?php

use model\repositorio\LojaRepositorio;

require_once __DIR__ . '/../../../model/repositorio/LojaRepositorio.php';
require_once __DIR__ . '/../../../controller/conexao-bd.php';

$repositorio = new LojaRepositorio($pdo);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$loja = $id > 0 ? $repositorio->buscarPorId($id) : null;
$todasLojas = $id === 0 ? $repositorio->buscarlojasFiltro(TipoLoja::Restaurante) : [];

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nossas Lojas - Shopping</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/index.css">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/lojas.css">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/logoShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/telaDeLoja.css">
</head>
<body>

<?php include(__DIR__ . '/../header.html'); ?>

<main>
    <div class="header-container">
        <a href="/SistemaShopping_web1/index.php" class="btn-voltar">← Voltar para Início</a>
        <h1 class="titulo-principal">Restaurantes</h1>
        <div class="espacador"></div>
    </div>

    <?php if (!$loja && empty($todasLojas)) : ?>
        <div class="sem-dados" style="max-width:900px;margin:40px auto;text-align:center;">
            Nenhum restaurante cadastrado ainda.
        </div>
    <?php elseif ($loja) : ?>
        <div class="loja-card">
            <?php
            $imgSrc = '';
            $nomeArquivo = $loja->getNomeImagem();
            $tipo = $loja->getTipoImagem() ?? 'image/png';
            $imgBase64 = $loja->getImagem() ?? '';

            if (!empty($nomeArquivo)) {
                $imgSrc = '/SistemaShopping_web1/img/lojas/' . ltrim($nomeArquivo, '/');
            } elseif (!empty($imgBase64)) {
                $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
            }
            ?>

            <?php if ($imgSrc !== ''): ?>
                <img src="<?php echo $imgSrc ?>" alt="Imagem da loja <?php echo htmlspecialchars($loja->getNome()) ?>">
            <?php else: ?>
                <div class="placeholder">Sem imagem</div>
            <?php endif; ?>

            <div class="loja-info">
                <h1><?php echo htmlspecialchars($loja->getNome()) ?></h1>
                <p class="descricao"><?php echo nl2br(htmlspecialchars($loja->getDescricao())) ?></p>

                <div class="meta">
                    <p><strong>Categoria:</strong> <?php echo htmlspecialchars($loja->getCategoria()) ?></p>
                    <p><strong>Localização:</strong> <?php echo htmlspecialchars($loja->getPosicao()) ?></p>
                    <p><strong>Telefone:</strong> <?php echo htmlspecialchars($loja->getTelefoneContato() ?? '-') ?></p>
                    <p><strong>CNPJ:</strong> <?php echo htmlspecialchars($loja->getCnpj() ?? '-') ?></p>
                    <p><strong>Tipo:</strong> <?php echo htmlspecialchars($loja->getTipoLoja()->value ?? '-') ?></p>
                    <p><strong>Horário de Funcionamento:</strong> <?php
                        $horarios = $loja->getHorarioFuncionamento();
                        foreach ($horarios as $horarioFuncionamento) {
                            $horarioInicial = $horarioFuncionamento->getHorarioInicial() ?? '00:00';
                            $horarioFinal = $horarioFuncionamento->getHorarioFinal() ?? '00:00';
                            $diaSemana = $horarioFuncionamento->getDiaSemana();
                            echo '<br/>' . $diaSemana . ": " . substr($horarioInicial, 0, 5) . ' até ' . substr($horarioFinal, 0, 5);
                        }
                        ?></p>
                </div>
            </div>
        </div>
    <?php else : ?>
        <div class="lojas-grid">
            <?php foreach ($todasLojas as $loja) : ?>
                <a href="?id=<?php echo $loja->getId() ?>" class="loja-card-small">
                    <?php
                    $imgSrc = '';
                    $nomeArquivo = $loja->getNomeImagem();
                    $tipo = $loja->getTipoImagem() ?? 'image/*';
                    $imgBase64 = $loja->getImagem() ?? '';

                    if (!empty($nomeArquivo)) {
                        $imgSrc = '/SistemaShopping_web1/img/lojas/' . ltrim($nomeArquivo, '/');
                    } elseif (!empty($imgBase64)) {
                        $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
                    }
                    ?>

                    <div class="img-container">
                        <?php if ($imgSrc !== ''): ?>
                            <img src="<?php echo $imgSrc ?>"
                                 alt="Imagem da loja <?php echo htmlspecialchars($loja->getNome()) ?>">
                        <?php else: ?>
                            <div class="placeholder">Sem imagem</div>
                        <?php endif; ?>
                    </div>
                    <h2><?php echo htmlspecialchars($loja->getNome()) ?></h2>
                    <p class="descricao"><?php echo nl2br(htmlspecialchars($loja->getDescricao())) ?></p>
                    <div class="meta">
                        <p><strong>Categoria:</strong> <?php echo htmlspecialchars($loja->getCategoria()) ?></p>
                        <p><strong>Localização:</strong> <?php echo htmlspecialchars($loja->getPosicao()) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
<?php include(__DIR__ . '/../footer.html'); ?>
</body>
</html>
