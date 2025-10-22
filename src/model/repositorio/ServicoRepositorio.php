<?php

namespace model\repositorio;

use model\servico\Servico;
use PDO;

class ServicoRepositorio
{
    private PDO $pdo;

    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function setarDadosStatement(string $sql, Servico $servico)
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $servico->getNome());
        $stmt->bindValue(2, $servico->getDescricao());
        $stmt->bindValue(3, $servico->getImagem());
        $stmt->bindValue(4, $servico->getDataRegistro()->format('Y-m-d H:i:s'));
        $stmt->bindValue(5, $servico->getId());
        return $stmt;
    }
}