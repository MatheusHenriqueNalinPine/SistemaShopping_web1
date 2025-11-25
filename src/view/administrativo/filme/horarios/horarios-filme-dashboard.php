<?php

use model\repositorio\HorarioFilmeRepositorio;
use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . '/../../../../model/repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../../../../model/repositorio/HorarioFilmeRepositorio.php';
require_once __DIR__ . '/../../../../model/repositorio/CinemaRepositorio.php';
require_once __DIR__ . '/../../../../controller/conexao-bd.php';

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

$repositorio = new HorarioFilmeRepositorio($pdo);
$horarios = $repositorio->buscarTodos();

$filmesRepo = new \model\repositorio\CinemaRepositorio($pdo);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Horários - Administrativo</title>
    <link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
    <link rel="stylesheet" href="/SistemaShopping_web1/css/crud-tabela.css">
</head>
<body>

<?php include('../../menu.php') ?>
<?php include('../../sidebar.php') ?>

<main class="conteudo">
    <h2>Gerenciamento de Horários</h2>

    <div class="acoes">
        <a href="cadastrar-horario.php" class="btn-cadastrar">Cadastrar horário</a>
    </div>

    <?php if (isset($_GET['erro']) && $_GET['erro'] === 'exclusao'): ?>
        <p class="mensagem-erro">Erro ao excluir horário selecionado.</p>
    <?php endif; ?>

    <table class="tabela">
        <thead>
        <tr>
            <th>Filme</th>
            <th>Data e Hora</th>
            <th>Sala</th>
            <th>Formato</th>
            <th>Modo Exibição</th>
            <th>Remover</th>
            <th>Editar</th>
        </tr>
        </thead>
        <tbody>

        <?php if (count($horarios) == 0) : ?>
            <tr>
                <td colspan="7" class="sem-dados">Nenhum horário cadastrado</td>
            </tr>
        <?php else: ?>
            <?php foreach ($horarios as $h): ?>
                <tr>
                    <td><?= htmlspecialchars($filmesRepo->buscarPorId($h->getIdFilme())->getNome()) ?></td>
                    <td><?= htmlspecialchars($h->getDataHora()->format('d/m/Y H:i')) ?></td>
                    <td><?= htmlspecialchars($h->getSala()) ?></td>
                    <td><?= htmlspecialchars($h->getFormatoFilme()->value) ?></td>
                    <td><?= htmlspecialchars($h->getModoExibicao()) ?></td>

                    <td>
                        <form action="/SistemaShopping_web1/src/controller/exclusao/excluir-horario-filme.php" method="post">
                            <input type="hidden" name="id_filme" value="<?= htmlspecialchars($h->getIdFilme()) ?>">
                            <input type="hidden" name="data_hora" value="<?= htmlspecialchars($h->getDataHora()->format('Y-m-d H:i:s')) ?>">
                            <input type="hidden" name="sala_filme" value="<?= htmlspecialchars($h->getSala()) ?>">

                            <input type="submit" class="botao-excluir" value="Excluir">
                        </form>
                    </td>

                    <td>
                        <form action="editar-horario.php" method="get">
                            <input type="hidden" name="id_filme" value="<?= htmlspecialchars($h->getIdFilme()) ?>">
                            <input type="hidden" name="data_hora" value="<?= htmlspecialchars($h->getDataHora()->format('Y-m-d H:i:s')) ?>">
                            <input type="hidden" name="sala_filme" value="<?= htmlspecialchars($h->getSala()) ?>">

                            <input type="submit" class="btn-editar" value="Editar">
                        </form>
                    </td>

                </tr>
            <?php endforeach; ?>
        <?php endif; ?>

        </tbody>
    </table>
</main>
</body>
</html>
