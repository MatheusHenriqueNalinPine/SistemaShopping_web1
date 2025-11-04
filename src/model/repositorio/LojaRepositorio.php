<?php

/*Referências:
 * Transaction: Conhecimento prévio no uso de java com hibernate, adaptando
 * para a sintaxe do PHP com base em: https://neon.com/postgresql/postgresql-php/transaction
 * (try-beginTransaction-commit-catch-rollback)
 * */

namespace model\repositorio;

use Exception;
use HorarioFuncionamento;
use Loja;
use PDO;
use TipoLoja;

use DateTime;

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

    public function salvar(Loja $loja): int
    {
        try {
            $this->pdo->beginTransaction();



            

            $sql = "insert into tbservico (nome, descricao, imagem, tipo_imagem, nome_imagem, url_imagem, data_registro) values (?, ?, ?, ?, ?, ?, default)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $loja->getNome());
            $stmt->bindValue(2, $loja->getDescricao());
            $stmt->bindValue(3, base64_decode($loja->getImagem()), PDO::PARAM_LOB);
            $stmt->bindValue(4, $loja->getTipoImagem());
            $stmt->bindValue(5, $loja->getNomeImagem());
            $stmt->bindValue(6, $loja->getUrlImagem());
            $stmt->execute();

            $idServico = $this->pdo->lastInsertId();

            $sql = "insert into tbloja (id, categoria ,posicao, telefone_contato, cnpj, loja_restaurante) values (?, ?, ?, ?, ?, ?);                                                                  ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $idServico);
            $stmt->bindValue(2, $loja->getCategoria());
            $stmt->bindValue(3, $loja->getPosicao());
            $stmt->bindValue(4, $loja->getTelefoneContato());
            $stmt->bindValue(5, $loja->getCnpj());
            $stmt->bindValue(6, $loja->getTipoLoja()->value);
            $stmt->execute();

            $sql = "insert into tbhorariofuncionamento values (?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $loja->getHorarioFuncionamento()->getHorarioInicial());
            $stmt->bindValue(2, $loja->getHorarioFuncionamento()->getHorarioFinal());
            $stmt->bindValue(3, $idServico);
            $stmt->execute();

            $this->pdo->commit();
            return (int)$idServico;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public
    function alterarLoja(Loja $loja)
    {
        $sql = "UPDATE tbservico SET nome = ?, descricao = ?, imagem = ?, tipo_imagem = ?, nome_imagem = ?, url_imagem = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $loja->getNome());
        $stmt->bindValue(2, $loja->getDescricao());
        $stmt->bindValue(3, base64_decode($loja->getImagem()), PDO::PARAM_LOB);
        $stmt->bindValue(4, $loja->getTipoImagem());
        $stmt->bindValue(5, $loja->getNomeImagem());
        $stmt->bindValue(6, $loja->getUrlImagem());
        $stmt->bindValue(7, $loja->getId());
        $stmt->execute();

        $sql = "update tbloja set posicao = ?, categoria = ?, telefone_contato = ?, cnpj = ?, loja_restaurante = ? where id = ?;                                                                  ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $loja->getPosicao());
        $stmt->bindValue(2, $loja->getCategoria());
        $stmt->bindValue(3, $loja->getTelefoneContato());
        $stmt->bindValue(4, $loja->getCnpj());
        $stmt->bindValue(5, $loja->getTipoLoja()->value);
        $stmt->bindValue(6, $loja->getId());
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

    public function buscarLojas(): array
    {
        try {
            $sql = "SELECT tbServico.id, tbServico.nome, tbLoja.categoria, 
                           tbHorarioFuncionamento.horario_inicial, tbHorarioFuncionamento.horario_final,
                           tbLoja.cnpj, tbLoja.loja_restaurante, tbLoja.categoria,
                           tbLoja.telefone_contato, tbServico.descricao, tbServico.imagem,
                           tbServico.tipo_imagem, tbServico.nome_imagem, tbServico.url_imagem,
                           tbLoja.posicao, tbServico.data_registro
                    FROM tbServico 
                    INNER JOIN tbLoja ON tbLoja.id = tbServico.id 
                    LEFT JOIN tbHorarioFuncionamento ON tbHorarioFuncionamento.id_servico = tbServico.id 
                    ORDER BY tbLoja.id ASC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result_set = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return array_map(fn($result) => $this->formarObjeto($result), $result_set);
        } catch (Exception $e) {
            
            error_log("Erro ao buscar lojas: " . $e->getMessage());
            return [];
        }
    }

    public
    function formarObjeto(array $result): Loja
    {
        return new Loja($result['id'],
            $result['nome'] ?? null,
            $result['descricao'] ?? null,
            base64_encode($result['imagem'] ?? ''),
            $result['tipo_imagem'] ?? 'image/png',
            $result['nome_imagem'] ?? null,
            $result['url_imagem'] ?? null,
            new DateTime($result['data_registro'] ?? 'now'),
            $result['posicao'] ?? null,
            $result['telefone_contato'] ?? null,
            $result['cnpj'] ?? null,
            $result['categoria'] ?? 'Acessórios',
            TipoLoja::from($result['loja_restaurante']),
            new HorarioFuncionamento($result['horario_inicial'], $result['horario_final']),);

    }

    public function buscarPorId(int $id): ?Loja
    {
        $sql = "select tbServico.id, tbServico.nome, tbLoja.categoria, tbHorarioFuncionamento.horario_inicial, tbHorarioFUncionamento.horario_final, tbLoja.cnpj, tbLoja.loja_restaurante, tbLoja.categoria, tbLoja.telefone_contato, tbServico.descricao, tbServico.imagem, tbServico.tipo_imagem, tbServico.nome_imagem, tbServico.url_imagem, tbLoja.posicao " .
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

    public function cnpjExists(string $cnpj): bool
    {
        $sql = "select cnpj from tbLoja where cnpj = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $cnpj);
        $stmt->execute();
        return (bool)$stmt->fetchColumn();
    }
}