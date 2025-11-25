<?php

use model\repositorio\UsuarioRepositorio;

require_once __DIR__ . "/../../../model/repositorio/UsuarioRepositorio.php";
require_once __DIR__ . "/../../../model/usuario/Usuario.php";
require_once __DIR__ . "/../../../controller/conexao-bd.php";

// carregar sempre as categorias disponíveis (se a tabela existir) e exibir o select
$categorias = [];
try {
    // tenta buscar diretamente da tabela de categorias de filme
    $stmt = $pdo->query("SELECT id, categoria FROM tbcategoriafilme ORDER BY categoria");
    $fetched = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (is_array($fetched)) {
        $categorias = $fetched;
    }
} catch (Throwable $e) {
    // tabela/tudo ausente: mantém $categorias vazio e registra log
    error_log("Falha ao carregar tbcategoriafilme: " . $e->getMessage());
    $categorias = [];
}

// --- Início: carregar salas e formatos e evitar warnings ---
$salas = [];
$formatos = [];
try {
    $stmt = $pdo->query("SELECT id, nome FROM tbsala ORDER BY nome");
    $salas = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Throwable $e) {
    error_log("Falha ao carregar tbsala: " . $e->getMessage());
    $salas = [];
}

try {
    $stmt = $pdo->query("SELECT id, formato FROM tbformatofilme ORDER BY formato");
    $formatos = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
} catch (Throwable $e) {
    error_log("Falha ao carregar tbformatofilme: " . $e->getMessage());
    $formatos = [];
}

$hasPosicao = count($salas) > 0;
$hasFormato = count($formatos) > 0;
// --- Fim: carregamento ---

session_start();
$usuario_logado = $_SESSION['usuario'] ?? null;

if (!$usuario_logado) {
    header('Location: /SistemaShopping_web1/src/view/sessoes/login.php?erro=deslogado');
    exit;
}

$repositorio = new UsuarioRepositorio($pdo);
$usuario = (new UsuarioRepositorio($pdo))->buscarPorEmail($usuario_logado);

$erro = $_GET['erro'] ?? null;
$sucesso = $_GET['sucesso'] ?? null;

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
            <?php if ($sucesso === '1'): ?>
                <p class="mensagem-sucesso">Filme cadastrado com sucesso.</p>
            <?php endif; ?>
            <?php if (isset($_GET['erro']) && $_GET['erro'] === 'sql'): ?>
                <p class="mensagem-erro">Erro ao salvar. Tente novamente. Se o problema persistir, contate o administrador.</p>
                <?php
                // Exibe a mensagem técnica apenas para administradores (apenas você verá)
                // Ajuste a comparação de cargo se o valor real do administrador for diferente.
                if (isset($usuario) && method_exists($usuario, 'getCargo') && $usuario->getCargo() === 'administrador' && !empty($_SESSION['ultimo_erro'])): ?>
                    <pre class="mensagem-aviso" style="white-space:pre-wrap;"><?php echo htmlspecialchars($_SESSION['ultimo_erro']); ?></pre>
                    <?php unset($_SESSION['ultimo_erro']); ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (isset($_GET['erro']) && $_GET['erro'] === 'campos-vazios'): ?>
                <p class="mensagem-erro">Não deixe os campos vazios.</p>
            <?php endif; ?>
            <h2>Cadastro de Filme</h2>
            <form action="/SistemaShopping_web1/src/controller/cadastro/registrar_filme.php" method="post"
                  enctype="multipart/form-data">
                <label for="nomeCinema">Nome do Filme</label>
                <input type="text" id="nomeCinema" name="nome" placeholder="Digite o nome do filme" required>

                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria">
                    <option value="">Selecione a categoria</option>
                    <?php if (!empty($categorias)): ?>
                        <?php foreach ($categorias as $c): ?>
                            <?php $catId = $c['id'] ?? ($c[0] ?? ''); $catNome = $c['categoria'] ?? ($c['nome'] ?? ($c[1] ?? '')); ?>
                            <option value="<?php echo htmlspecialchars($catId, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($catNome, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- se não houver categorias, usuário pode criar em filme/categoria/cadastrar-categoria.php -->
                    <?php endif; ?>
                </select>

                <label for="sala">Sala de transmissão</label>
                <textarea id="sala" name="sala" placeholder="Digite a sala que será transmitido (ex: Sala 3)" rows="1"></textarea>
                <!-- campo hidden para compatibilidade com controllers que esperam 'posicao' -->
                <input type="hidden" id="posicao_hidden" name="posicao" value="">

                <label for="descricao">Descreva o filme</label>
                <textarea id="descricao" name="descricao" placeholder="Descreva o filme..." rows="4"></textarea>

                <?php if ($hasFormato): ?>
                    <label for="formato">Formato do filme</label>
                    <select id="formato" name="formato" required>
                        <option value="" disabled selected>Selecione o formato do filme</option>
                        <?php foreach ($formatos as $f): ?>
                            <?php $fId = $f['id'] ?? ($f[0] ?? ''); $fNome = $f['formato'] ?? ($f[1] ?? ''); ?>
                            <!-- usar o nome do formato como value também -->
                            <option value="<?php echo htmlspecialchars($fNome, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($fNome, ENT_QUOTES, 'UTF-8'); ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php else: ?>
                    <label for="formato">Formato do filme</label>
                    <select id="formato" name="formato" required>
                        <option value="" disabled selected>Selecione o formato do filme</option>
                        <option value="Dublado">Dublado</option>
                        <option value="Legendado">Legendado</option>
                    </select>
                <?php endif; ?>

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

                <input type="hidden" id="id" name="id" value="0"/>
                <input class="btn-cadastrar" type="submit" value="Cadastrar"/>
            </form>
        </div>
    </section>
</main>
<script>
    // copia o texto digitado em 'sala' para o hidden 'posicao' no submit
    (function(){
        const form = document.querySelector('form[action="/SistemaShopping_web1/src/controller/cadastro/registrar_filme.php"]');
        if (!form) return;
        form.addEventListener('submit', function() {
            const salaEl = document.getElementById('sala');
            const posHidden = document.getElementById('posicao_hidden');
            if (salaEl && posHidden) {
                posHidden.value = salaEl.value.trim();
            }
        });
    })();
</script>
</body>

</html>
