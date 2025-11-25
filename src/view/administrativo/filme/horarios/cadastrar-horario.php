<?php

use model\repositorio\UsuarioRepositorio;
use model\repositorio\HorarioFilmeRepositorio;
use model\repositorio\CinemaRepositorio;
use model\servico\filme\FormatoFilme;

require_once __DIR__ . "/../../../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../../../model/repositorio/HorarioFilmeRepositorio.php";
require_once __DIR__ . "/../../../../model/repositorio/CinemaRepositorio.php";
require_once __DIR__ . "/../../../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../../../controller/conexao-bd.php";

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorioUsuario = new UsuarioRepositorio($pdo);
$usuario = $repositorioUsuario->buscarPorEmail($usuario_logado);

$erro = $_GET['erro'] ?? null;

$cargo = $usuario->getCargo();
if ($cargo == Cargo::Lojista || $cargo == Cargo::Gerenciador_anuncio) {
    header('Location: /SistemaShopping_web1/src/view/administrativo/administrativo.php');
    exit;
}

$repositorioFilme = new CinemaRepositorio($pdo);
$filmes = $repositorioFilme->buscarTodos(); 
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Horário de Filme - SchweizerPine Shopping</title>
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/cadastrar.css">
</head>

<body>
<?php include('../../menu.php') ?>
<main>
    <section class="cadastro-container">
        <div class="form-box">

            <?php if ($erro === 'campos-vazios'): ?>
                <p class="mensagem-erro">Não deixe os campos vazios.</p>
            <?php endif; ?>

            <h2>Cadastrar Horário de Filme</h2>

            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_horario_filme.php" method="post">
                <label for="id_filme">Selecione o Filme</label>
                <select id="id_filme" name="id_filme" required>
                    <option value="">Selecione</option>
                    <?php foreach ($filmes as $f): ?>
                        <option value="<?= htmlspecialchars($f->getId()) ?>">
                            <?= htmlspecialchars($f->getNome()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="data_hora">Data e Hora da Sessão</label>
                <input type="datetime-local" id="data_hora" name="data_hora" required>

                <label for="sala">Sala</label>
                <input type="number" id="sala" name="sala" min="1" required>

                <label for="formato">Formato (Legendado/Dublado)</label>
                <select id="formato" name="formato" required>
                    <option value="">Selecione</option>
                    <option value="leg">Legendado</option>
                    <option value="dub">Dublado</option>
                </select>

                <label for="modo">Modo de Exibição</label>
                <select id="modo" name="modo_exibicao" required>
                    <option value="">Selecione</option>
                    <option value="2D">2D</option>
                    <option value="3D">3D</option>
                    <option value="IMAX">IMAX</option>
                </select>

                <input type="hidden" name="id" value="0">

                <input class="btn-cadastrar" type="submit" value="Cadastrar"/>
            </form>
        </div>
    </section>
</main>

</body>
</html>
