<?php

require_once __DIR__ . '/../../../model/repositorio/UsuarioRepositorio.php';
require_once __DIR__ . '/../../../controller/conexao-bd.php';

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;
if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$usuario = (new \model\repositorio\UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);
$cargo = $usuario->getCargo();
if ($cargo == Cargo::Funcionario_cinema || $cargo == Cargo::Gerenciador_anuncio) {
    header('Location: /SistemaShopping_web1/src/view/administrativo/administrativo.php');
    exit;
}

// listar filmes diretamente de tbfilme
$filmes = [];
try {
    $sql = "SELECT f.id, f.nome, f.formato, f.sala, f.horarios, cf.categoria AS categoria_nome
            FROM tbfilme f
            LEFT JOIN tbcategoriafilme cf ON f.id_categoria_filme = cf.id
            ORDER BY f.nome";
    $stmt = $pdo->query($sql);
    $filmes = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    error_log("Falha ao carregar filmes via PDO: " . $e->getMessage());
    $filmes = [];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Gerenciar Filmes - Administrativo</title>
	<link rel="icon" type="image/png" href="/SistemaShopping_web1/img/iconShopping.png">
	<link rel="stylesheet" href="/SistemaShopping_web1/css/crud-tabela.css">
</head>
<body>
<?php include('../menu.php') ?>
<?php include('../sidebar.php') ?>

<main class="conteudo">
	<h2>Gerenciamento de Filmes</h2>

	<div class="acoes">
		<a href="cadastrar-filme.php" class="btn-cadastrar">Cadastrar filme</a>
	</div>

	<table class="tabela">
		<thead>
		<tr>
			<th>Nome</th>
            <th>Categoria</th>
            <th>Sala de transmissão</th>
            <th>Formato</th>
			<th>Remover</th>
			<th>Editar</th>
		</tr>
		</thead>
		<tbody>
		<?php if (count($filmes) == 0) : ?>
			<tr>
				<td colspan="6" class="sem-dados">Nenhum filme cadastrado</td>
			</tr>
		<?php else: ?>
			<?php foreach ($filmes as $f): ?>
				<tr>
					<td><?php echo htmlspecialchars($f['nome'] ?? '') ?></td>
					<td><?php echo htmlspecialchars($f['categoria_nome'] ?? '') ?></td>
					<td><?php echo htmlspecialchars($f['sala'] ?? $f['posicao'] ?? '') ?></td>
					<td><?php echo htmlspecialchars($f['formato'] ?? '') ?></td>
					<td>
						<form action="/SistemaShopping_web1/src/controller/exclusao/excluir-filme.php" method="post">
							<input type="hidden" name="id" value="<?php echo htmlspecialchars($f['id']) ?>">
							<input type="submit" class="botao-excluir" value="Excluir">
						</form>
					</td>
					<td>
						<form action="editar-filme.php" method="get">
							<input type="hidden" name="id" value="<?php echo htmlspecialchars($f['id']) ?>">
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
