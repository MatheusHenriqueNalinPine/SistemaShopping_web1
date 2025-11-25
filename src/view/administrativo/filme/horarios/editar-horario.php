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

$id = $_GET['id_filme'] ?? null;
$dataHora = $_GET['data_hora'] ?? null;
$sala = $_GET['sala_filme'] ?? null;

$repositorio = new HorarioFilmeRepositorio($pdo);
$horario = $repositorio->buscarPorChavePrimaria($id, new DateTime($dataHora), $sala);

$filmes = (new CinemaRepositorio($pdo))->buscarTodos();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Horário de Filme - SchweizerPine Shopping</title>
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

            <h2>Editar Horário de Filme</h2>

            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_horario_filme.php" method="post">
                <label for="id_filme">Selecione o Filme</label>
                <select id="id_filme" name="id_filme" required>
                    <option value="">Selecione</option>
                    <?php foreach ($filmes as $f): ?>
                        <option value="<?= htmlspecialchars($f->getId()) ?>"
                                <?= $horario && $horario->getIdFilme() == $f->getId() ? 'selected' : '' ?>>
                            <?= htmlspecialchars($f->getNome()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="data_hora">Data e Hora da Sessão</label>
                <input type="datetime-local" id="data_hora" name="data_hora"
                       value="<?= $horario ? $horario->getDataHora()->format('Y-m-d\TH:i') : '' ?>" required>

                <label for="sala">Sala</label>
                <input type="number" id="sala" name="sala" min="1"
                       value="<?= $horario ? $horario->getSala() : '' ?>" required>

                <label for="formato">Formato (Legendado/Dublado)</label>
                <select id="formato" name="formato" required>
                    <option value="">Selecione</option>
                    <option value="leg" <?= $horario && $horario->getFormatoFilme()->value === 'leg' ? 'selected' : '' ?>>Legendado</option>
                    <option value="dub" <?= $horario && $horario->getFormatoFilme()->value === 'dub' ? 'selected' : '' ?>>Dublado</option>
                </select>

                <label for="modo_exibicao">Modo de Exibição</label>
                <select id="modo_exibicao" name="modo_exibicao" required>
                    <option value="">Selecione</option>
                    <option value="2D" <?= $horario && $horario->getModoExibicao() === '2D' ? 'selected' : '' ?>>2D</option>
                    <option value="3D" <?= $horario && $horario->getModoExibicao() === '3D' ? 'selected' : '' ?>>3D</option>
                    <option value="IMAX" <?= $horario && $horario->getModoExibicao() === 'IMAX' ? 'selected' : '' ?>>IMAX</option>
                </select>

                <input type="hidden" name="id" value="<?= $horario ? $horario->getIdFilme() : 0 ?>">

                <input class="btn-cadastrar" type="submit" value="Atualizar"/>
            </form>
        </div>
    </section>
</main>

</body>
</html>
