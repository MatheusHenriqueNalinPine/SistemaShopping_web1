<?php

use model\repositorio\LojaRepositorio;

require_once __DIR__ . '/../../../model/repositorio/LojaRepositorio.php';
require_once __DIR__ . '/../../../controller/conexao-bd.php';

$repositorio = new LojaRepositorio($pdo);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$loja = $id > 0 ? $repositorio->buscarPorId($id) : null;
$todasLojas = $id === 0 ? $repositorio->buscarlojasFiltro(TipoLoja::Loja) : [];


$selectedLimit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;


$anunciosUltimos = [];
if ($selectedLimit > 0) {
    function fetchLatestAds(PDO $pdo, int $limit): array {
        $tables = ['anuncio', 'anuncios', 'anuncios_anuncio'];
        foreach ($tables as $t) {
            try {
                $sql = "SELECT id, nome, descricao, imagem, tipo_imagem, nome_imagem, url_imagem, data_registro
                        FROM {$t}
                        ORDER BY data_registro DESC
                        LIMIT :limit";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    return $rows;
                }
            } catch (\Throwable $e) {
                // tenta próxima tabela
                continue;
            }
        }
        return [];
    }

    try {
        $anunciosUltimos = fetchLatestAds($pdo, $selectedLimit);
    } catch (\Throwable $e) {
        $anunciosUltimos = [];
    }
}


$selectedShopLimit = isset($_GET['shop_limit']) ? (int)$_GET['shop_limit'] : 0;
$selectedShopPage = isset($_GET['shop_page']) ? max(1, (int)$_GET['shop_page']) : 1;
$ultimasLojas = [];
$totalShopPages = 0;
$totalLojas = 0;
if ($selectedShopLimit > 0) {
    try {
        $totalLojas = $repositorio->contarLojas();
        $offset = ($selectedShopPage - 1) * $selectedShopLimit;
        $ultimasLojas = $repositorio->buscarlojasPaginadas($selectedShopLimit, $offset);
        $totalShopPages = (int)ceil($totalLojas / $selectedShopLimit);
    } catch (\Throwable $e) {
        $ultimasLojas = [];
        $totalShopPages = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lojas - SchweizerPine Shopping</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/index.css">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/lojas.css">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/telaDeLoja.css">
</head>
<body>

<?php include(__DIR__ . '/../header.html') ?>

<main>
    <div class="header-container">
        <a href="/SistemaShopping_web1/index.php" class="btn-voltar">← Voltar para Início</a>
        <h1 class="titulo-principal">Lojas</h1>

        
        <div class="select-container" style="margin-left:16px;">
            <label for="limitSelect" style="margin-right:8px;">Ver últimos anúncios:</label>
            <select id="limitSelect" name="limit"
                    onchange="if(this.value>0){ window.location.href='?limit='+this.value; } else { window.location.href='?'; }">
                <option value="0" <?php echo $selectedLimit === 0 ? 'selected' : '' ?>>Selecione...</option>
                <option value="5" <?php echo $selectedLimit === 5 ? 'selected' : '' ?>>Últimos 5</option>
                <option value="10" <?php echo $selectedLimit === 10 ? 'selected' : '' ?>>Últimos 10</option>
                <option value="15" <?php echo $selectedLimit === 15 ? 'selected' : '' ?>>Últimos 15</option>
            </select>
        </div>

        
        <div class="select-container" style="margin-left:16px;">
            <label for="shopLimitSelect" style="margin-right:8px;">Ver últimos cadastros de lojas:</label>
            <select id="shopLimitSelect" name="shop_limit"
                    onchange="{
                        const limit = this.value;
                        if(limit>0){
                            
                            const params = new URLSearchParams(window.location.search);
                            params.set('shop_limit', limit);
                            params.set('shop_page', 1);
                            
                            window.location.href = window.location.pathname + '?' + params.toString();
                        } else {
                            window.location.href = window.location.pathname;
                        }
                    }">
                <option value="0" <?php echo $selectedShopLimit === 0 ? 'selected' : '' ?>>Selecione...</option>
                <option value="5" <?php echo $selectedShopLimit === 5 ? 'selected' : '' ?>>Últimos 5</option>
                <option value="10" <?php echo $selectedShopLimit === 10 ? 'selected' : '' ?>>Últimos 10</option>
                <option value="15" <?php echo $selectedShopLimit === 15 ? 'selected' : '' ?>>Últimos 15</option>
            </select>
        </div>

        <div class="espacador"></div>
    </div>

    <?php if (!$loja && empty($todasLojas)) : ?>
        <div class="sem-dados">
            Nenhuma loja cadastrada ainda.
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

    <!-- NOVO: seção para mostrar as últimas lojas selecionadas (paginada) -->
    <?php if ($selectedShopLimit > 0): ?>
        <section class="ultimas-lojas" style="padding:16px; border:1px solid #ddd; margin:16px;">
            <h2>Últimas <?php echo htmlspecialchars($selectedShopLimit); ?> lojas cadastradas (página <?php echo $selectedShopPage; ?> de <?php echo max(1, $totalShopPages); ?>)</h2>
            <?php if (empty($ultimasLojas)): ?>
                <p>Nenhuma loja encontrada.</p>
            <?php else: ?>
                <div class="lojas-grid" style="display:flex; gap:12px; flex-wrap:wrap;">
                    <?php foreach ($ultimasLojas as $loja): 
                        $imgSrc = '';
                        $nomeArquivo = $loja->getNomeImagem();
                        $tipo = $loja->getTipoImagem() ?? 'image/png';
                        $imgBase64 = $loja->getImagem() ?? '';
                        if (!empty($nomeArquivo)) {
                            $imgSrc = '/SistemaShopping_web1/img/lojas/' . ltrim($nomeArquivo, '/');
                        } elseif (!empty($imgBase64)) {
                            $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
                        }
                        $lojaId = $loja->getId();
                    ?>
                        <a href="?id=<?php echo (int)$lojaId ?>" class="loja-card-small" style="width:200px; text-decoration:none; color:inherit;">
                            <div class="img-container" style="height:120px; overflow:hidden;">
                                <?php if ($imgSrc !== ''): ?>
                                    <img src="<?php echo $imgSrc ?>" alt="Imagem da loja <?php echo htmlspecialchars($loja->getNome()) ?>" style="width:100%; height:100%; object-fit:cover;">
                                <?php else: ?>
                                    <div class="placeholder" style="width:100%; height:120px; display:flex;align-items:center;justify-content:center;">Sem imagem</div>
                                <?php endif; ?>
                            </div>
                            <h2 style="font-size:16px; margin:8px 0 4px;"><?php echo htmlspecialchars($loja->getNome()) ?></h2>
                            <p class="descricao" style="font-size:12px; color:#666; height:36px; overflow:hidden;"><?php echo nl2br(htmlspecialchars(substr($loja->getDescricao(), 0, 150))) ?></p>
                            <div class="meta" style="font-size:12px; color:#444;">
                                <p><strong>Categoria:</strong> <?php echo htmlspecialchars($loja->getCategoria()) ?></p>
                                <p><strong>Localização:</strong> <?php echo htmlspecialchars($loja->getPosicao()) ?></p>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- PAGINAÇÃO -->
                <?php if ($totalShopPages > 1): ?>
                    <nav class="paginacao" style="margin-top:12px; display:flex; gap:8px; align-items:center;">
                        <?php
                        // função auxiliar para montar querystring preservando limit de anúncios (limit)
                        $buildPageUrl = function($page) use ($selectedShopLimit, $selectedLimit) {
                            $params = [];
                            $params['shop_limit'] = $selectedShopLimit;
                            $params['shop_page'] = $page;
                            if (!empty($selectedLimit)) $params['limit'] = $selectedLimit;
                            return '?' . http_build_query($params);
                        };
                        ?>

                        <?php if ($selectedShopPage > 1): ?>
                            <a href="<?php echo $buildPageUrl($selectedShopPage - 1); ?>">« Anterior</a>
                        <?php endif; ?>

                        <?php
                        // exibir páginas (limitar visualização se muitas páginas)
                        $start = max(1, $selectedShopPage - 3);
                        $end = min($totalShopPages, $selectedShopPage + 3);
                        for ($p = $start; $p <= $end; $p++): ?>
                            <?php if ($p == $selectedShopPage): ?>
                                <strong><?php echo $p; ?></strong>
                            <?php else: ?>
                                <a href="<?php echo $buildPageUrl($p); ?>"><?php echo $p; ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>

                        <?php if ($selectedShopPage < $totalShopPages): ?>
                            <a href="<?php echo $buildPageUrl($selectedShopPage + 1); ?>">Próximo »</a>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <?php if ($selectedLimit > 0): ?>
        <section class="ultimos-anuncios" style="padding:16px; border:1px solid #ddd; margin:16px;">
            <h2>Últimos <?php echo htmlspecialchars($selectedLimit); ?> anúncios</h2>
            <?php if (empty($anunciosUltimos)): ?>
                <p>Nenhum anúncio encontrado.</p>
            <?php else: ?>
                <div class="anuncios-list" style="display:flex; gap:12px; flex-wrap:wrap;">
                    <?php foreach ($anunciosUltimos as $anuncio): 
                        $imgSrc = '';
                        $nomeArquivo = $anuncio['nome_imagem'] ?? '';
                        $tipo = $anuncio['tipo_imagem'] ?? 'image/png';
                        $imgBase64 = $anuncio['imagem'] ?? '';
                        if (!empty($nomeArquivo)) {
                            $imgSrc = '/SistemaShopping_web1/img/anuncios/' . ltrim($nomeArquivo, '/');
                        } elseif (!empty($imgBase64)) {
                            $imgSrc = 'data:' . $tipo . ';base64,' . $imgBase64;
                        }
                        $anuncioId = $anuncio['id'] ?? 0;
                    ?>
                        <div class="anuncio-item" style="width:200px; border:1px solid #eee; padding:8px;">
                            <?php if ($imgSrc !== ''): ?>
                                <img src="<?php echo $imgSrc ?>" alt="<?php echo htmlspecialchars($anuncio['nome'] ?? '') ?>" style="width:100%; height:120px; object-fit:cover;">
                            <?php else: ?>
                                <div style="width:100%; height:120px; background:#f4f4f4; display:flex;align-items:center;justify-content:center;">Sem imagem</div>
                            <?php endif; ?>
                            <h3 style="font-size:14px; margin:8px 0;"><?php echo htmlspecialchars($anuncio['nome'] ?? '') ?></h3>
                            <p style="font-size:12px; color:#666; height:36px; overflow:hidden;"><?php echo nl2br(htmlspecialchars(substr($anuncio['descricao'] ?? '', 0, 150))) ?></p>
                            <!-- link para detalhe (ajuste se houver rota pública de detalhe) -->
                            <a href="/SistemaShopping_web1/src/view/administrativo/anuncio/visualizar-anuncio.php?id=<?php echo (int)$anuncioId ?>" style="font-size:12px; color:#007bff;">Ver anúncio</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>
</main>
<?php include(__DIR__ . '/../footer.html'); ?>
</body>
</html>
