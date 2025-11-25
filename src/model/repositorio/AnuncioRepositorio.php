<?php

namespace model\repositorio;
require_once(__DIR__ . '/../servico/anuncio/Anuncio.php');
require_once(__DIR__ . '/../servico/Servico.php');
require_once(__DIR__ . '/../servico/anuncio/FormatoAnuncio.php');

use DateTime;
use Exception;
use model\servico\anuncio\Anuncio;
use model\servico\anuncio\FormatoAnuncio;
use PDO;
use PDOStatement;

class AnuncioRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function buscarPorNome($nome): ?Anuncio
    {
        $stmt = $this->pdo->prepare("select a.id, s.descricao, s.imagem," . "s.data_registro, a.formato_anuncio, a.categoria_anuncio from tbanuncio a inner join tbservico s on a.id = s.id" . "where s.nome = :nome limit 20");
        $stmt->bindParam(':nome', $nome);
        $stmt->execute();
        $dados = $stmt->fetch();
        return $dados ? $this->formarObjeto($dados) : null;
    }

    private function formarObjeto(array $dados): Anuncio
    {
        return new Anuncio($dados['id'] ?? null, $dados["nome"] ?? '', $dados["descricao"] ?? '', $dados["imagem"] ?? '', $dados["imagem"] ?? '', $dados["nome_imagem"] ?? '', $dados["url_imagem"] ?? '', new DateTime($dados['data_registro'] ?? 'now'), FormatoAnuncio::from($dados['formato_anuncio'] ?? $dados['formato'] ?? 'quadrado'), $dados['id_categoria_anuncio'] ?? 0);
    }

    public function salvar(string $nome, string $descricao, string $imagem, string $tipoimagem, string $nomeimagem, string $urlimagem, DateTime $data_registro, FormatoAnuncio $formato_anuncio, string $categoria_anuncio)
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

            $sql = "select id from tbCategoriaAnuncio where horarios = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $categoria_anuncio);
            $stmt->execute();
            $idCategoria = $stmt->fetchColumn();

            if (!$idCategoria) {
                $sql = "insert into tbCategoriaAnuncio (horarios) values (?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(1, $categoria_anuncio);
                $stmt->execute();
                $idCategoria = $this->pdo->lastInsertId();
            }

            $sql = "insert into tbanuncio (id, formato_anuncio, id_categoria_anuncio) values (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $id);
            $stmt->bindValue(2, $formato_anuncio->value);
            $stmt->bindValue(3, $idCategoria);
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

            $sql = "select id from tbCategoriaAnuncio where horarios = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $anuncio->getCategoriaAnuncio());
            $stmt->execute();
            $idCategoria = $stmt->fetchColumn();

            if (!$idCategoria) {
                $sql = "insert into tbCategoriaAnuncio (horarios) values (?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(1, $anuncio->getCategoriaAnuncio());
                $stmt->execute();
                $idCategoria = $this->pdo->lastInsertId();
            }

            $sql = "update tbanuncio set formato_anuncio = ?, id_categoria_anuncio = ? where id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $anuncio->getFormatoAnuncio()->value);
            $stmt->bindValue(2, $idCategoria);
            $stmt->bindValue(3, $anuncio->getId());
            $stmt->execute();

            $sql = "update tbservico set nome = ?, descricao = ?, imagem = ?, url_imagem = ?, tipo_imagem =? , nome_imagem =?, data_registro = ? where id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $anuncio->getNome());
            $stmt->bindValue(2, $anuncio->getDescricao());
            $stmt->bindValue(3, $anuncio->getImagem());
            $stmt->bindValue(4, $anuncio->getUrlImagem());
            $stmt->bindValue(5, $anuncio->getTipoImagem());
            $stmt->bindValue(6, $anuncio->getNomeImagem());
            $stmt->bindValue(7, $anuncio->getDataRegistro()->format('Y-m-d H:i:s'));
            $stmt->bindValue(8, $anuncio->getId());
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

    public function buscarTodos(): array
    {
        $sql = "select a.id, s.nome, s.descricao, s.imagem, s.nome_imagem, s.tipo_imagem, s.url_imagem, s.data_registro,
                   a.formato_anuncio as formato, a.id_categoria_anuncio
            from tbanuncio a
            inner join tbservico s on a.id = s.id
            order by s.data_registro desc";
        $result_set = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    public function buscaranunciosFiltro(FormatoAnuncio $tipo)
    {
        $sql = "select tbservico.id, tbservico.nome, tbanuncio.formato_anuncio, tbanuncio.id_categoria_anuncio,
                   tbservico.descricao, tbservico.imagem, tbservico.tipo_imagem, 
                   tbservico.nome_imagem, tbservico.url_imagem, tbservico.data_registro
            from tbanuncio 
            inner join tbservico on tbanuncio.id = tbservico.id 
            inner join tbcategoriaanuncio on tbanuncio.id_categoria_anuncio = tbcategoriaanuncio.id 
            where tbanuncio.formato_anuncio = :tipo
            order by tbanuncio.id asc";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tipo' => $tipo->value]);
        $result_set = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    public function buscarAnunciosCarrossel(): array
    {
        $sql = "select a.id, s.nome, s.descricao, s.imagem, s.tipo_imagem, s.nome_imagem, s.url_imagem, s.data_registro,
                   a.formato_anuncio as formato, a.id_categoria_anuncio as horarios
            from tbanuncio a
            inner join tbservico s on a.id = s.id
            where a.formato_anuncio = 'Carrossel'
            order by s.data_registro desc";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $result_set = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    public function buscarAnunciosMinimizados(int $limite): array
    {
        $sql = "select a.id, s.nome, s.descricao, s.imagem, s.nome_imagem, s.tipo_imagem, s.url_imagem, s.data_registro,
                   a.formato_anuncio as formato, a.id_categoria_anuncio as horarios
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
        $sql = "select a.id, s.nome, s.descricao, s.imagem, s.nome_imagem, s.tipo_imagem, s.url_imagem, s.data_registro,
                   a.formato_anuncio as formato, a.id_categoria_anuncio as horarios
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
        $sql = "select s.id, s.nome, a.id_categoria_anuncio, a.formato_anuncio, s.descricao, s.imagem, s.url_imagem, s.tipo_imagem, s.nome_imagem, s.data_registro from tbanuncio a inner join tbServico s on a.id = s.id where s.id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->formarObjeto($result);
    }

    public function getCategoriaById(int $id): string
    {
        $sql = "select categoria from tbcategoriaanuncio where id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_COLUMN);

    }

    private function setarDadosStatement(string $sql, Anuncio $anuncio): PDOStatement|false
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $anuncio->getFormatoAnuncio()->value);
        $stmt->bindValue(2, $anuncio->getCategoriaAnuncio());
        return $stmt;
    }
}