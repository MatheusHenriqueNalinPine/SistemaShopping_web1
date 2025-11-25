<?php

namespace model\repositorio;

use PDO;

class CategoriaLojaRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function salvar(string $categoria)
    {
        $sql = "insert into tbcategorialoja (categoria) values (?);";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $categoria);
        $stmt->execute();
    }

    public function alterar(int $id, string $categoria)
    {
        $sql = "update tbcategorialoja set categoria = ? where id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $categoria);
        $stmt->bindValue(2, $id);
        $stmt->execute();
    }


    public function excluir(int $id): void
    {
        $sql = "delete from tbcategorialoja where id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
    }


    public function buscarTodas(): array
    {
        $sql = "select * from tbcategorialoja;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id)
    {
        $sql = "select categoria from tbcategorialoja where id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function isCategoriaUsed(int $id): bool
    {
        $sql = "SELECT 1 FROM tbLoja WHERE id_categoria = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    public function whoUse(int $id)
    {
        $sql = "SELECT tbservico.nome FROM tbloja inner join tbservico on tbloja.id = tbservico.id
                      WHERE id_categoria = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}