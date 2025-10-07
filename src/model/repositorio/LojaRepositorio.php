<?php

namespace model\repositorio;

use Loja;
use PDO;

class LojaRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function criarLoja(Loja $loja) {
        $sql = "insert into tbloja (posicao, telefone_contato, cnpj, loja_restaurante) values (?, ?, ?, ?);                                                                  ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $loja->getPosicao());
        $stmt->bindValue(2, $loja->getTelefoneContato());
        $stmt->bindValue(3, $loja->getCnpj());
        $stmt->bindValue(4, \TipoLoja::from($loja->getTipoLoja()));
    }

    public function alterarLoja(Loja $loja) {

    }

    public function excluirLoja(Loja $loja) {

    }

    public function buscarLojas() {

    }
}