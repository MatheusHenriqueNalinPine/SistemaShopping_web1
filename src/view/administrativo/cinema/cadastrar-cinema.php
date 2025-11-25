<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../../controller/conexao-bd.php";

// carrega categorias de filme: tenta repositório se existir, senão fallback PDO
$categoriaRepoFile = __DIR__ . '/../../../model/repositorio/CategoriaFilmeRepositorio.php';
$categorias = [];

if (file_exists($categoriaRepoFile)) {
    require_once $categoriaRepoFile;
    if (class_exists('\model\repositorio\CategoriaFilmeRepositorio')) {
        try {
            $categorias = (new \model\repositorio\CategoriaFilmeRepositorio($pdo))->buscarTodas();
        } catch (Throwable $e) {
            error_log("Erro ao buscar categorias via repositório: " . $e->getMessage());
            $categorias = [];
        }
    } else {
        error_log("Classe CategoriaFilmeRepositorio não encontrada mesmo com o arquivo presente: $categoriaRepoFile");
    }
}

// fallback: se $categorias vazio, tenta consultar diretamente a tabela tbcategoriafilme
if (empty($categorias)) {
    try {
        $stmt = $pdo->query("SELECT id, categoria FROM tbcategoriafilme ORDER BY categoria");
        $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (is_array($fetched)) $categorias = $fetched;
    } catch (Throwable $e) {
        error_log("Falha ao carregar categorias de filme via PDO: " . $e->getMessage());
        $categorias = [];
    }
}

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new UsuarioRepositorio($pdo);
$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);

$erro = $_GET['erro'] ?? null;

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
    <title>Cadastrar Cinema - SchweizerPine Shopping</title>
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
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
            <h2>Cadastro de Filme</h2>
            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_filme.php" method="post"
                  enctype="multipart/form-data">
                <label for="nomeCinema">Nome do Filme</label>
                <input type="text" id="nomeCinema" name="nome" placeholder="Digite o nome do cinema" required>


                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" required>
                    <option value="">Selecione a categoria</option>
                    <?php foreach ($categorias as $c): ?>
                        <?php
                            $catId = $c['id'] ?? ($c[0] ?? '');
                            $catNome = $c['categoria'] ?? ($c['nome'] ?? ($c[1] ?? ''));
                        ?>
                        <option value="<?php echo htmlspecialchars($catId, ENT_QUOTES, 'UTF-8'); ?>">
                            <?php echo htmlspecialchars($catNome, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="sala">Sala de transmissão</label>
                <input type="text" id="posicao" name="posicao" placeholder="Ex.: Sala 3"></input>

                <label for="descricao">Descreva o filme</label>
                <textarea id="descricao" name="descricao" placeholder="Descreva o cinema..." rows="4"></textarea>


                <label for="imagem">Faça aqui o upload da capa do filme</label>
                <input type="file" id="imagem" name="imagem" accept="image/*">


                <input type="hidden" id="id" name="id" value="0"/>
                <input class="btn-cadastrar" type="submit" value="Cadastrar"/>

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

            </form>
        </div>
    </section>
</main>

</body>

</html>
