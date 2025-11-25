<?php
namespace model\repositorio;

use PDO;

class CategoriaCinemaRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function salvar(string $categoria): void
    {
        $sql = "INSERT INTO tbcategoriafilme (horarios) VALUES (?);";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $categoria);
        $stmt->execute();
    }

    public function alterar(int $id, string $categoria): void
    {
        $sql = "UPDATE tbcategoriafilme SET horarios = ? WHERE id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $categoria);
        $stmt->bindValue(2, $id);
        $stmt->execute();
    }

    public function excluir(int $id): void
    {
        $sql = "DELETE FROM tbcategoriafilme WHERE id = ?;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
    }

    public function buscarTodas(): array
    {
        $sql = "SELECT * FROM tbcategoriafilme ORDER BY horarios;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id)
    {
        $sql = "SELECT horarios FROM tbcategoriafilme WHERE id = ? LIMIT 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function buscarPorCategoria(string $categoria)
    {
        $sql = "SELECT * FROM tbcategoriafilme WHERE horarios = ? LIMIT 1;";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $categoria);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isCategoriaUsed(int $id): bool
    {
        $sql = "SELECT 1 FROM tbfilme WHERE id_categoria_filme = :id LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    public function whoUse(int $id): array
    {
        $sql = "SELECT nome FROM tbfilme WHERE id_categoria_filme = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
