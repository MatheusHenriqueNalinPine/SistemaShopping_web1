<?php

namespace model\repositorio;

use Exception;
use HorarioFuncionamento;
use Loja;
use PDO;
use TipoLoja;

require_once __DIR__ . "/../servico/loja/Loja.php";
require_once __DIR__ . "/../servico/loja/TipoLoja.php";
require_once __DIR__ . "/../servico/HorarioFuncionamento.php";

class LojaRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

       public function salvar(Loja $loja)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "insert into tbservico (nome, descricao, imagem, data_registro, tipo_imagem) values (?, ?, ?, default, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $loja->getNome());
            $stmt->bindValue(2, $loja->getDescricao());
            $stmt->bindValue(3, $loja->getImagem());
            $stmt->bindValue(4, $loja->getTipoImagem());
            $stmt->execute();

            $idServico = $this->pdo->lastInsertId();

            $sql = "insert into tbloja (id, posicao, telefone_contato, cnpj, loja_restaurante) values (?, ?, ?, ?, ?);                                                                  ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $idServico);
            $stmt->bindValue(2, $loja->getPosicao());
            $stmt->bindValue(3, $loja->getTelefoneContato());
            $stmt->bindValue(4, $loja->getCnpj());
            $stmt->bindValue(5, $loja->getTipoLoja()->value);
            $stmt->execute();

            $sql = "insert into tbhorariofuncionamento values (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $loja->getHorarioFuncionamento()->getHorarioInicial());
            $stmt->bindValue(2, $loja->getHorarioFuncionamento()->getHorarioFinal());
            $stmt->bindValue(3, $idServico);
            $stmt->execute();

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public
    function alterarLoja(Loja $loja)
    {
        $sql = "UPDATE tbservico SET nome = ?, descricao = ?, imagem = ?, tipo_imagem = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $loja->getNome());
        $stmt->bindValue(2, $loja->getDescricao());
        $stmt->bindValue(3, $loja->getImagem());
        $stmt->bindValue(4, $loja->getTipoImagem());
        $stmt->bindValue(5, $loja->getId());
        $stmt->execute();

        $sql = "update tbloja set posicao = ?, telefone_contato = ?, cnpj = ?, loja_restaurante = ? where id = ?;                                                                  ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $loja->getPosicao());
        $stmt->bindValue(2, $loja->getTelefoneContato());
        $stmt->bindValue(3, $loja->getCnpj());
        $stmt->bindValue(4, $loja->getTipoLoja()->value);
        $stmt->bindValue(5, $loja->getId());
        $stmt->execute();

        $sql = "update tbhorariofuncionamento set horario_inicial = ?, horario_final = ?, id_servico = ? where " .
            "horario_inicial = ? and horario_final = ? and id_servico = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $loja->getHorarioFuncionamento()->getHorarioInicial());
        $stmt->bindValue(2, $loja->getHorarioFuncionamento()->getHorarioFinal());
        $stmt->bindValue(3, $loja->getId());
        $stmt->bindValue(4, $loja->getHorarioFuncionamento()->getHorarioInicial());
        $stmt->bindValue(5, $loja->getHorarioFuncionamento()->getHorarioFinal());
        $stmt->bindValue(6, $loja->getId());
        $stmt->execute();
    }

    public
    function excluirLoja(int $id): void
    {
        $sql = "delete from tbhorariofuncionamento where id_servico = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();

        $sql = "delete from tbLoja where id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();

        $sql = "delete from tbservico where id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
    }

    public
    function buscarLojas(): array
    {
        $sql = "select tbServico.id, tbServico.nome, tbLoja.categoria, tbHorarioFuncionamento.horario_inicial, tbHorarioFUncionamento.horario_final, tbLoja.cnpj, tbLoja.loja_restaurante, tbLoja.categoria, tbLoja.telefone_contato, tbServico.descricao, tbServico.imagem, tbServico.tipo_imagem, tbLoja.posicao " .
            "from tbloja inner join tbServico on tbLoja.id = tbServico.id " .
            "inner join tbHorarioFuncionamento on tbHorarioFuncionamento.id_servico = tbServico.id order by tbLoja.id asc";
        $result_set = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    public
    function formarObjeto(array $result): Loja
    {
        return new Loja($result['id'],
            $result['nome'] ?? null,
            $result['descricao'] ?? null,
            $result['imagem'] ?? null,
            $result['tipo-imagem'] ?? 'image/png',
            $result['posicao'] ?? null,
            $result['telefone_contato'] ?? null,
            $result['cnpj'] ?? null,
            $result['categoria'] ?? 'Acessórios',
            TipoLoja::from($result['loja_restaurante']),
            new HorarioFuncionamento($result['horario_inicial'], $result['horario_final']),);

    }

    public function buscarPorId(int $id): ?Loja
    {
        $sql = "select tbServico.id, tbServico.nome, tbLoja.categoria, tbHorarioFuncionamento.horario_inicial, tbHorarioFUncionamento.horario_final, tbLoja.cnpj, tbLoja.loja_restaurante, tbLoja.categoria, tbLoja.telefone_contato, tbServico.descricao, tbServico.imagem, tbServico.tipo_imagem, tbLoja.posicao " .
            "from tbloja inner join tbServico on tbLoja.id = tbServico.id " .
            "inner join tbHorarioFuncionamento on tbHorarioFuncionamento.id_servico = tbServico.id " .
            "where tbServico.id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        return $this->formarObjeto($result);
    }

    public function cnpjExists(string $cnpj): bool {
        $sql = "select cnpj from tbLoja where cnpj = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $cnpj);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }
}