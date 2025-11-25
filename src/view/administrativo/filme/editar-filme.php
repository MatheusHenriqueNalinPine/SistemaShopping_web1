<?php
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../../controller/conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;
if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);
$cargo = $usuario->getCargo();
if ($cargo == Cargo::Funcionario_cinema || $cargo == Cargo::Gerenciador_anuncio) {
    header('Location: /SistemaShopping_web1/src/view/administrativo/administrativo.php');
    exit;
}

// id do filme a editar
$idFilme = $_GET['id'] ?? null;
if (empty($idFilme)) {
    echo '<p style="color:red;padding:20px;">ID do filme não informado.</p>';
    exit;
}

// tenta carregar repositório de filme se existir (nome de arquivo comum)
$filme = null;
$filmeRepoFile = __DIR__ . '/../../../model/repositorio/FilmeRepositorio.php';
if (file_exists($filmeRepoFile)) {
    require_once $filmeRepoFile;
}
// procura qualquer classe carregada com "FilmeRepositorio"
$filmeClass = null;
foreach (get_declared_classes() as $decl) {
    if (stripos($decl, 'FilmeRepositorio') !== false) {
        $filmeClass = $decl;
        break;
    }
}
if ($filmeClass) {
    try {
        $repo = new $filmeClass($pdo);
        if (method_exists($repo, 'buscarPorId')) {
            $filme = $repo->buscarPorId($idFilme);
        } elseif (method_exists($repo, 'findById')) {
            $filme = $repo->findById($idFilme);
        }
    } catch (Throwable $e) {
        error_log("Erro ao usar $filmeClass: " . $e->getMessage());
        $filme = null;
    }
}

// fallback PDO direto se não obteve via repositório
if (!$filme) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM tbfilme WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $idFilme]);
        $filme = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (Throwable $e) {
        error_log("Falha ao buscar filme via PDO id={$idFilme}: " . $e->getMessage());
        $filme = null;
    }
}

if (!$filme) {
    echo '<p style="color:red;padding:20px;">Filme não encontrado para edição.</p>';
    exit;
}

// carregar categorias (repositório ou fallback PDO)
$categorias = [];
$categoriaRepoFile = __DIR__ . '/../../../model/repositorio/CategoriaFilmeRepositorio.php';
if (file_exists($categoriaRepoFile)) require_once $categoriaRepoFile;
$categoriaClass = null;
foreach (get_declared_classes() as $decl) {
    if (stripos($decl, 'Categoria') !== false && stripos($decl, 'filme') !== false) {
        $categoriaClass = $decl;
        break;
    }
}
if ($categoriaClass) {
    try {
        $categorias = (new $categoriaClass($pdo))->buscarTodas();
    } catch (Throwable $e) {
        error_log("Erro ao carregar categorias via repo: " . $e->getMessage());
        $categorias = [];
    }
}
if (count($categorias) === 0) {
    try {
        $stmt = $pdo->query("SELECT id, categoria FROM tbcategoriafilme ORDER BY categoria");
        $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Throwable $e) {
        $categorias = [];
    }
}

// carregar salas e formatos (fallback PDO)
$salas = [];
$formatos = [];
try {
    $stmt = $pdo->query("SELECT id, nome FROM tbsala ORDER BY nome");
    $salas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Throwable $e) {
    $salas = [];
}
try {
    $stmt = $pdo->query("SELECT id, formato FROM tbformatofilme ORDER BY formato");
    $formatos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Throwable $e) {
    $formatos = [];
}

// extrair campos do registro (suporta array/objeto)
$filme_id = '';
$filme_nome = '';
$filme_categoria = '';
$filme_posicao = '';
$filme_descricao = '';
$filme_formato = '';
$filme_horarios = [];

if (is_array($filme)) {
    $filme_id = $filme['id'] ?? '';
    $filme_nome = $filme['nome'] ?? '';
    $filme_categoria = $filme['id_categoria_filme'] ?? $filme['categoria'] ?? '';
    $filme_posicao = $filme['sala'] ?? $filme['posicao'] ?? '';
    $filme_descricao = $filme['descricao'] ?? $filme['descricao_filme'] ?? '';
    $filme_formato = $filme['formato'] ?? '';
    $rawHorarios = $filme['horarios'] ?? '';
    // tenta JSON
    $decoded = json_decode($rawHorarios, true);
    if (is_array($decoded)) $filme_horarios = $decoded;
} elseif (is_object($filme)) {
    if (method_exists($filme, 'getId')) $filme_id = $filme->getId();
    if (method_exists($filme, 'getNome')) $filme_nome = $filme->getNome();
    if (method_exists($filme, 'getCategoriaId')) $filme_categoria = $filme->getCategoriaId();
    if (method_exists($filme, 'getPosicao')) $filme_posicao = $filme->getPosicao();
    if (method_exists($filme, 'getSala')) $filme_posicao = $filme->getSala();
    if (method_exists($filme, 'getDescricao')) $filme_descricao = $filme->getDescricao();
    if (method_exists($filme, 'getFormato')) $filme_formato = $filme->getFormato();
    if (method_exists($filme, 'getHorarios')) {
        $raw = $filme->getHorarios();
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) $filme_horarios = $decoded;
    }
}
// helper para horários com default
function horario_valor($horarios, $dia, $campo, $default) {
    if (isset($horarios[$dia]) && is_array($horarios[$dia]) && !empty($horarios[$dia][$campo])) {
        return $horarios[$dia][$campo];
    }
    return $default;
}

// valores padrão caso não existam horários
$def = [
    'domingo' => ['abertura' => '10:00', 'fechamento' => '19:30'],
    'segunda'  => ['abertura' => '10:00', 'fechamento' => '20:00'],
    'terca'    => ['abertura' => '10:00', 'fechamento' => '20:00'],
    'quarta'   => ['abertura' => '10:00', 'fechamento' => '20:00'],
    'quinta'   => ['abertura' => '10:00', 'fechamento' => '20:00'],
    'sexta'    => ['abertura' => '10:00', 'fechamento' => '20:00'],
    'sabado'   => ['abertura' => '10:00', 'fechamento' => '22:00'],
];

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Filme - SchweizerPine Shopping</title>
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/cadastrar.css">
</head>
<body>
<?php include('../menu.php') ?>
<main>
    <section class="cadastro-container">
        <div class="form-box">
            <h2>Editar Filme</h2>
            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_filme.php" method="post" enctype="multipart/form-data">
                <label for="nomeCinema">Nome do Filme</label>
                <input type="text" id="nomeCinema" name="nome" placeholder="Digite o nome do filme" required
                       value="<?php echo htmlspecialchars($filme_nome, ENT_QUOTES, 'UTF-8'); ?>">

                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria">
                    <option value="">Selecione a categoria</option>
                    <?php foreach ($categorias as $c): 
                        $catId = $c['id'] ?? ($c[0] ?? '');
                        $catNome = $c['categoria'] ?? ($c['nome'] ?? ($c[1] ?? ''));
                        $sel = (($catId == $filme_categoria) || ($catNome == $filme_categoria)) ? 'selected' : '';
                    ?>
                        <option value="<?php echo htmlspecialchars($catId ?: $catNome, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $sel; ?>>
                            <?php echo htmlspecialchars($catNome, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="posicao">Sala de transmissão</label>
                <?php if (!empty($salas)): ?>
                    <select id="posicao" name="posicao">
                        <option value="">Selecione a sala</option>
                        <?php foreach ($salas as $s): 
                            $sNome = $s['nome'] ?? ($s[1] ?? '');
                            $sel = ($sNome == $filme_posicao) ? 'selected' : '';
                        ?>
                            <option value="<?php echo htmlspecialchars($sNome, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($sNome, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <input type="text" id="posicao" name="posicao" placeholder="Ex.: Sala 3" value="<?php echo htmlspecialchars($filme_posicao, ENT_QUOTES, 'UTF-8'); ?>">
                <?php endif; ?>

                <label for="descricao">Descreva o filme</label>
                <textarea id="descricao" name="descricao" placeholder="Descreva o filme..." rows="4"><?php echo htmlspecialchars($filme_descricao, ENT_QUOTES, 'UTF-8'); ?></textarea>

                <label for="formato">Formato do filme</label>
                <?php if (!empty($formatos)): ?>
                    <select id="formato" name="formato" required>
                        <option value="" disabled>Selecione o formato do filme</option>
                        <?php foreach ($formatos as $f):
                            $fname = $f['formato'] ?? ($f[1] ?? '');
                            $sel = ($fname === $filme_formato) ? 'selected' : '';
                        ?>
                            <option value="<?php echo htmlspecialchars($fname, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($fname, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <select id="formato" name="formato" required>
                        <option value="" disabled>Selecione o formato do filme</option>
                        <option value="Dublado" <?php echo ($filme_formato === 'Dublado') ? 'selected' : ''; ?>>Dublado</option>
                        <option value="Legendado" <?php echo ($filme_formato === 'Legendado') ? 'selected' : ''; ?>>Legendado</option>
                    </select>
                <?php endif; ?>

                <label for="imagem">Faça aqui o upload da capa do filme (opcional)</label>
                <input type="file" id="imagem" name="imagem" accept="image/*">

                <table class="horario">
                    <thead><tr><th>Horários de transmissão</th></tr></thead>
                    <tbody>
                    <?php foreach ($def as $dia => $vals): 
                        $ab = horario_valor($filme_horarios, $dia, 'abertura', $vals['abertura']);
                        $fe = horario_valor($filme_horarios, $dia, 'fechamento', $vals['fechamento']);
                    ?>
                        <tr>
                            <td class="dia"><?php echo ucfirst($dia); ?></td>
                            <td><input type="time" name="abertura[<?php echo $dia; ?>]" value="<?php echo htmlspecialchars($ab); ?>"></td>
                            <td><input type="time" name="fechamento[<?php echo $dia; ?>]" value="<?php echo htmlspecialchars($fe); ?>"></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>

                <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($filme_id, ENT_QUOTES, 'UTF-8'); ?>"/>
                <input class="btn-cadastrar" type="submit" value="Salvar alterações"/>
            </form>
        </div>
    </section>
</main>
</body>
</html>
