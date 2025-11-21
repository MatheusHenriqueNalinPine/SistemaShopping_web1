<?php

namespace model\repositorio;

use PDO;

class CategoriaAnuncioRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function salvar(string $categoria)
    {
        $sql = "insert into tbcategoriaanuncio (categoria) values (?);";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $categoria);
        $stmt->execute();
    }

    public function alterar(int $id, string $categoria)
    {
        $sql = "update tbcategoriaanuncio set categoria = ? where id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $categoria);
        $stmt->bindValue(2, $id);
        $stmt->execute();
    }


    public function excluir(int $id): void
    {
        $sql = "delete from tbcategoriaanuncio where id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
    }


    public function buscarTodas(): array
    {
        $sql = "select * from tbcategoriaanuncio;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id)
    {
        $sql = "select categoria from tbcategoriaanuncio where id = ? limit 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function buscarPorCategoria(string $categoria)
    {
        $sql = "select * from tbcategoriaanuncio where categoria = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $categoria);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function isCategoriaUsed(int $id): bool
    {
        $sql = "SELECT 1 FROM tbAnuncio WHERE id_categoria_anuncio = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    public function whoUse(int $id)
    {
        $sql = "SELECT tbservico.nome FROM tbAnuncio inner join tbservico on tbAnuncio.id = tbservico.id
                      WHERE id_categoria_anuncio = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}