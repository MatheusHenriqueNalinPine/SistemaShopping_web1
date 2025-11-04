<?php

namespace model\repositorio;
require_once(__DIR__ . '/../servico/anuncio/Anuncio.php');
require_once(__DIR__ . '/../servico/Servico.php');
require_once(__DIR__ . '/../servico/anuncio/FormatoAnuncio.php');
require_once(__DIR__ . '/ServicoRepositorio.php');

use DateTime;
use model\servico\anuncio\Anuncio;
use Exception;
use model\servico\anuncio\FormatoAnuncio;
use PDO;
use model\repositorio\ServicoRepositorio;
use PDOException;

class AnuncioRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function formarObjeto(array $dados): Anuncio
    {
        return new Anuncio(
            $dados['id'] ?? null,
            $dados["nome"] ?? '',
            $dados["descricao"] ?? '',
            $dados["imagem"] ?? '',
            new DateTime($dados['data_registro'] ?? 'now'),
            FormatoAnuncio::from($dados['formato_anuncio'] ?? $dados['formato'] ?? 'quadrado'),
            $dados['categoria'] ?? '');
    }

    public function buscarPorNome($nome): ?Anuncio
    {
        $stmt = $this->pdo->prepare("select a.id, s.descricao, s.imagem," .
            "s.data_registro, a.formato_anuncio, a.categoria_anuncio from tbanuncio a inner join tbservico s on a.id = s.id" .
            "where s.nome = :nome limit 20");
        $stmt->bindParam(':nome', $nome);
        $stmt->execute();
        $dados = $stmt->fetch();
        return $dados ? $this->formarObjeto($dados) : null;
    }

    public function salvar(string   $nome, string $descricao, string $imagem,
                           DateTime $data_registro, FormatoAnuncio $formato_anuncio, string $categoria_anuncio)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "insert into tbservico (nome, descricao, imagem, data_registro) values (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $nome);
            $stmt->bindValue(2, $descricao);
            $stmt->bindValue(3, $imagem);
            $stmt->bindValue(4, $data_registro->format('Y-m-d H:i:s'));
            $stmt->execute();

            $id = $this->pdo->lastInsertId();
            $sql = "insert into tbanuncio (id, formato_anuncio, id_categoria_anuncio) values (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $id);
            $stmt->bindValue(2, $formato_anuncio->value);
            $stmt->bindValue(3, $categoria_anuncio);
            $stmt->execute();
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function atualizar(Anuncio $anuncio)
    {
        try {
            $this->pdo->beginTransaction();
            $sql = "update tbanuncio set formato_anuncio = ?, id_categoria_anuncio = ? where id = ?";
            $stmt = $this->setarDadosStatement($sql, $anuncio);
            $stmt->bindValue(3, $anuncio->getId());
            $stmt->execute();

            $sql = "update tbservico set nome = ?, descricao = ?, imagem = ?, data_registro = ? where id = ?";
            $stmt = (new ServicoRepositorio($this->pdo))->setarDadosStatement($sql, $anuncio);
            $stmt->execute();
            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public
    function excluir(int $id): bool
    {
        try {
            $this->pdo->beginTransaction();
            $anuncio_sql = "delete from tbanuncio where id = ?";
            $anuncio_stmt = $this->pdo->prepare($anuncio_sql);
            $anuncio_stmt->execute([$id]);

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

    public
    function buscarTodos(): array
    {
        $sql = "select a.id, s.nome, s.descricao, s.imagem, s.data_registro,
                   a.formato_anuncio as formato, a.id_categoria_anuncio as categoria
            from tbanuncio a
            inner join tbservico s on a.id = s.id
            order by s.data_registro desc";
        $result_set = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    private
    function setarDadosStatement(string $sql, Anuncio $anuncio): \PDOStatement|false
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $anuncio->getFormatoAnuncio()->value);
        $stmt->bindValue(2, $anuncio->getCategoriaAnuncio());
        return $stmt;
    }

    public function buscarAnunciosMinimizados(int $limite): array
    {
        $sql = "select a.id, s.nome, s.descricao, s.imagem, s.data_registro,
                   a.formato_anuncio as formato, a.id_categoria_anuncio as categoria
            from tbanuncio a
            inner join tbservico s on a.id = s.id
            where a.formato_anuncio = 'Quadrado'
            order by s.data_registro desc
            limit :limite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        $result_set = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    public function buscarAnunciosHorizontais(int $limite): array
    {
        $sql = "select a.id, s.nome, s.descricao, s.imagem, s.data_registro,
                   a.formato_anuncio as formato, a.id_categoria_anuncio as categoria
            from tbanuncio a
            inner join tbservico s on a.id = s.id
            where a.formato_anuncio = 'Horizontal'
            order by s.data_registro desc
            limit :limite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        $result_set = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    public function buscarPorId(int $id): ?Anuncio
    {
        $sql = "select s.id, s.nome, a.categoria_anuncio, a.formato_anuncio, 
        s.descricao, s.imagem, s.data_registro " .
            "from tbanuncio a inner join tbServico s on a.id = s.id " .
            "where s.id = ?";

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