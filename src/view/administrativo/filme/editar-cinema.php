<?php

use model\repositorio\CategoriaCinemaRepositorio;
use model\repositorio\CinemaRepositorio;
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../../controller/conexao-bd.php";

// carrega CinemaRepositorio se existir
$cinemaRepoFile = __DIR__ . '/../../../model/repositorio/CinemaRepositorio.php';
$categoriaRepoFile = __DIR__ . '/../../../model/repositorio/CategoriaCinemaRepositorio.php';

if (file_exists($cinemaRepoFile)) {
    require_once $cinemaRepoFile;
} else {
    error_log("CinemaRepositorio.php não encontrado em: $cinemaRepoFile");
}

if (file_exists($categoriaRepoFile)) {
    require_once $categoriaRepoFile;
} else {
    error_log("CategoriaCinemaRepositorio.php não encontrado em: $categoriaRepoFile");
}

// tenta instanciar o repositório quando disponível, senão evita chamadas fatais
$repositorio = null;
$cinema = null;
if (class_exists('\model\repositorio\CinemaRepositorio')) {
    $repositorio = new \model\repositorio\CinemaRepositorio($pdo);
    $idCinema = $_GET['id'] ?? null;
    $cinema = $repositorio->buscarPorId($idCinema);
} else {
    // se não houver repositório, evita continuar; mostra mensagem simples e sai
    echo '<p style="color:red;padding:20px;">Repositório de Cinema não disponível. Verifique os arquivos do sistema.</p>';
    exit;
}

// se não veio id ou não encontrou, mostra erro simples e para (pode redirecionar para lista)
if (empty($idCinema) || !$cinema) {
    echo '<p style="color:red;padding:20px;">Cinema não encontrado para edição. Verifique o ID informado.</p>';
    exit;
}

// --- Início: extrair campos do cinema (suporta array ou objeto) ---
$cinema_id = '';
$cinema_nome = '';
$cinema_categoria = '';
$cinema_posicao = '';
$cinema_descricao = '';
$cinema_formato = '';

if (is_array($cinema)) {
    $cinema_id = $cinema['id'] ?? ($cinema['Id'] ?? '');
    $cinema_nome = $cinema['nome'] ?? ($cinema['titulo'] ?? '');
    $cinema_categoria = $cinema['categoria_id'] ?? $cinema['categoria'] ?? '';
    $cinema_posicao = $cinema['posicao'] ?? $cinema['sala'] ?? $cinema['sala_transmissao'] ?? '';
    $cinema_descricao = $cinema['descricao'] ?? '';
    $cinema_formato = $cinema['formato'] ?? '';
} elseif (is_object($cinema)) {
    // tenta vários getters comuns
    if (method_exists($cinema, 'getId')) $cinema_id = $cinema->getId();
    if (method_exists($cinema, 'getNome')) $cinema_nome = $cinema->getNome();
    if (method_exists($cinema, 'getTitulo') && !$cinema_nome) $cinema_nome = $cinema->getTitulo();
    if (method_exists($cinema, 'getCategoriaId')) $cinema_categoria = $cinema->getCategoriaId();
    if (method_exists($cinema, 'getCategoria') && !$cinema_categoria) $cinema_categoria = $cinema->getCategoria();
    if (method_exists($cinema, 'getPosicao')) $cinema_posicao = $cinema->getPosicao();
    if (method_exists($cinema, 'getSala')) $cinema_posicao = $cinema->getSala();
    if (method_exists($cinema, 'getDescricao')) $cinema_descricao = $cinema->getDescricao();
    if (method_exists($cinema, 'getFormato')) $cinema_formato = $cinema->getFormato();
}
// normaliza para string
$cinema_id = (string)$cinema_id;
$cinema_nome = (string)$cinema_nome;
$cinema_categoria = (string)$cinema_categoria;
$cinema_posicao = (string)$cinema_posicao;
$cinema_descricao = (string)$cinema_descricao;
$cinema_formato = (string)$cinema_formato;
// --- Fim: extração ---

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
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <title>Editar Cinema - SchweizerPine Shopping</title>
    <link rel="stylesheet" href="/SistemaShopping_web1/css/cadastrar.css">
</head>

<body>
<?php include('../menu.php') ?>
<main>
    <section class="cadastro-container">
        <div class="form-box">
            <?php if (isset($_GET['erro']) && $_GET['erro'] === 'campos-vazios'): ?>
                <p class="mensagem-erro">Não deixe os campos vazios.</p>
            <?php endif; ?>
            <h2>Editar Cinema</h2>
            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_cinema.php" method="post"
                  enctype="multipart/form-data">
                <label for="nomeCinema">Nome do Filme</label>
                <input type="text" id="nomeCinema" name="nome" placeholder="Digite o nome do cinema" required
                       value="<?php echo htmlspecialchars($cinema_nome, ENT_QUOTES, 'UTF-8'); ?>">

                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" required>
                    <option value="" disabled>Selecione a categoria</option>
                    <?php foreach ($categorias as $categoria): 
                        $catId = $categoria['id'] ?? ($categoria[0] ?? '');
                        $catNome = $categoria['categoria'] ?? ($categoria[1] ?? '');
                        $selected = ($catId == $cinema_categoria || $catNome == $cinema_categoria) ? 'selected' : '';
                    ?>
                        <option value="<?= htmlspecialchars($catId, ENT_QUOTES, 'UTF-8') ?>" <?= $selected ?>><?= htmlspecialchars($catNome, ENT_QUOTES, 'UTF-8') ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="sala">Sala de transmissão</label>
                <input type="text" id="posicao" name="posicao" placeholder="Ex.: Sala 3"
                       value="<?php echo htmlspecialchars($cinema_posicao, ENT_QUOTES, 'UTF-8'); ?>">

                <label for="descricao">Descreva o filme</label>
                <textarea id="descricao" name="descricao" placeholder="Descreva o cinema..." rows="4"><?php echo htmlspecialchars($cinema_descricao, ENT_QUOTES, 'UTF-8'); ?></textarea>

                <label for="formato">Formato do filme</label>
                <select id="formato" name="formato" required>
                    <option value="" disabled>Selecione o formato do filme</option>
                    <option value="Dublado" <?php echo ($cinema_formato === 'Dublado') ? 'selected' : ''; ?>>Dublado</option>
                    <option value="Legendado" <?php echo ($cinema_formato === 'Legendado') ? 'selected' : ''; ?>>Legendado</option>
                </select>

                
                <label for="imagem">Faça aqui o upload da capa do filme</label>
                <input type="file" id="imagem" name="imagem" accept="image/*">


                <table class="horario">
                    <thead>
                    <tr>
                        <th>Horários de transmissão</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td class="dia">Domingo</td>
                        <td><input type="time" name="abertura[domingo]" value="10:00"></td>
                        <td><input type="time" name="fechamento[domingo]" value="19:30"></td>
                    </tr>
                    <tr>
                        <td class="dia">Segunda</td>
                        <td><input type="time" name="abertura[segunda]" value="10:00"></td>
                        <td><input type="time" name="fechamento[segunda]" value="20:00"></td>
                    </tr>
                    <tr>
                        <td class="dia">Terça</td>
                        <td><input type="time" name="abertura[terca]" value="10:00"></td>
                        <td><input type="time" name="fechamento[terca]" value="20:00"></td>
                    </tr>
                    <tr>
                        <td class="dia">Quarta</td>
                        <td><input type="time" name="abertura[quarta]" value="10:00"></td>
                        <td><input type="time" name="fechamento[quarta]" value="20:00"></td>
                    </tr>
                    <tr>
                        <td class="dia">Quinta</td>
                        <td><input type="time" name="abertura[quinta]" value="10:00"></td>
                        <td><input type="time" name="fechamento[quinta]" value="20:00"></td>
                    </tr>
                    <tr>
                        <td class="dia">Sexta</td>
                        <td><input type="time" name="abertura[sexta]" value="10:00"></td>
                        <td><input type="time" name="fechamento[sexta]" value="20:00"></td>
                    </tr>
                    <tr>
                        <td class="dia">Sábado</td>
                        <td><input type="time" name="abertura[sabado]" value="10:00"></td>
                        <td><input type="time" name="fechamento[sabado]" value="22:00"></td>
                    </tr>
                    </tbody>
                </table>

                <input type="hidden" id="id" name="id" value="<?php echo htmlspecialchars($cinema_id, ENT_QUOTES, 'UTF-8'); ?>"/>
                <input class="btn-cadastrar" type="submit" value="Salvar alterações"/>

            </form>
        </div>
    </section>
</main>

</body>

</html>
