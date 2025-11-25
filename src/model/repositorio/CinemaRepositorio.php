<?php

namespace model\repositorio;
require_once(__DIR__ . '/../servico/filme/Filme.php');
require_once(__DIR__ . '/../servico/Servico.php');
require_once(__DIR__ . '/../servico/filme/FormatoFilme.php');

use DateTime;
use Exception;
use model\servico\filme\Filme;
use model\servico\filme\FormatoFilme;
use PDO;

class CinemaRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarPorNome($nome): ?Filme
    {
        $stmt = $this->pdo->prepare("select a.id, s.descricao, s.imagem," . "s.data_registro, a.formato_filme, a.categoria_filme from tbfilme a inner join tbservico s on a.id = s.id" . "where s.nome = :nome limit 20");
        $stmt->bindParam(':nome', $nome);
        $stmt->execute();
        $dados = $stmt->fetch();
        return $dados ? $this->formarObjeto($dados) : null;
    }

    private function formarObjeto(array $dados): Filme
    {
        return new Filme($dados['id'] ?? null, $dados["nome"] ?? '', $dados["descricao"] ?? '', $dados["imagem"] ?? '', $dados["tipo_imagem"] ?? '', $dados["nome_imagem"] ?? '', $dados["url_imagem"] ?? '', new DateTime($dados['data_registro'] ?? 'now'), $dados['genero'] ?? '');
    }

    public function salvar(string $nome, string $descricao, string $imagem, string $tipoimagem, string $nomeimagem, string $urlimagem, string $genero)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "insert into tbservico (nome, descricao, imagem, tipo_imagem, nome_imagem, url_imagem, data_registro) values (?, ?, ?, ?, ?, ?, default)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $nome);
            $stmt->bindValue(2, $descricao);
            $stmt->bindValue(3, $imagem);
            $stmt->bindValue(4, $tipoimagem);
            $stmt->bindValue(5, $nomeimagem);
            $stmt->bindValue(6, $urlimagem);
            $stmt->execute();

            $id = $this->pdo->lastInsertId();

            $sql = "insert into tbfilme (id, genero) values (?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $id);
            $stmt->bindValue(2, $genero);
            $stmt->execute();
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function atualizar(Filme $filme)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "update tbfilme set genero = ? where id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $filme->getGenero());
            $stmt->bindValue(2, $filme->getId());
            $stmt->execute();

            $sql = "update tbservico set nome = ?, descricao = ?, imagem = ?, url_imagem = ?, tipo_imagem =? , nome_imagem =?, data_registro = ? where id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $filme->getNome());
            $stmt->bindValue(2, $filme->getDescricao());
            $stmt->bindValue(3, $filme->getImagem());
            $stmt->bindValue(4, $filme->getUrlImagem());
            $stmt->bindValue(5, $filme->getTipoImagem());
            $stmt->bindValue(6, $filme->getNomeImagem());
            $stmt->bindValue(7, $filme->getDataRegistro()->format('Y-m-d H:i:s'));
            $stmt->bindValue(8, $filme->getId());
            $stmt->execute();
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function excluir(int $id): bool
    {
        try {
            $this->pdo->beginTransaction();
            $filme_sql = "delete from tbfilme where id = ?";
            $filme_stmt = $this->pdo->prepare($filme_sql);
            $filme_stmt->execute([$id]);

            $servico_sql = "delete from tbservico where id = ?";
            $servico_stmt = $this->pdo->prepare($servico_sql);
            $servico_stmt->execute([$id]);

            $this->pdo->commit();
            return true;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function buscarTodos(): array
    {
        $sql = "select a.id, s.nome, s.descricao, s.imagem, s.nome_imagem, s.tipo_imagem, s.url_imagem, s.data_registro,
                   a.genero
            from tbfilme a
            inner join tbservico s on a.id = s.id
            order by s.data_registro desc";
        $result_set = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    public function buscarPorId(int $id): ?Filme
    {
        $sql = "select s.id, s.nome, a.genero, s.descricao, s.imagem, s.url_imagem, s.tipo_imagem, s.nome_imagem, s.data_registro from tbfilme a inner join tbServico s on a.id = s.id where s.id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->formarObjeto($result);
    }
}