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

            $sql = "insert into tbloja (id, id_categoria ,posicao, telefone_contato, cnpj, loja_restaurante) values (?, ?, ?, ?, ?, ?);                                                                  ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $idServico);
            $stmt->bindValue(2, $loja->getCategoria());
            $stmt->bindValue(3, $loja->getPosicao());
            $stmt->bindValue(4, $loja->getTelefoneContato());
            $stmt->bindValue(5, $loja->getCnpj());
            $stmt->bindValue(6, $loja->getTipoLoja()->value);
            $stmt->execute();

            $horarioRepositorio = new HorarioFuncionamentoRepositorio($this->pdo);

            foreach ($loja->getHorarioFuncionamento() as $horario) {
                if (!$horarioRepositorio->verificarExistencia($horario)) {
                    $sql = "insert into tbhorariofuncionamento values (?, ?, ?)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindValue(1, $horario->getHorarioInicial());
                    $stmt->bindValue(2, $horario->getHorarioFinal());
                    $stmt->bindValue(3, $horario->getDiaSemana());
                    $stmt->execute();

                    $sql = "insert into tbhorarioservico (id, horario_inicial, horario_final, dia_semana, id_servico) values (?, ?, ?, ?, ?)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindValue(1, $horario->getHorarioInicial());
                    $stmt->bindValue(2, $horario->getHorarioFinal());
                    $stmt->bindValue(3, $horario->getDiaSemana());
                    $stmt->bindValue(4, $idServico);
                    $stmt->execute();
                }
            }

            $this->pdo->commit();
            return (int)$idServico;
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function alterarLoja(Loja $loja)
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "update tbservico set nome = ?, descricao = ?, imagem = ? where id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $loja->getNome());
            $stmt->bindValue(2, $loja->getDescricao());
            $stmt->bindValue(3, $loja->getImagem());
            $stmt->bindValue(4, $loja->getId());
            $stmt->execute();

            $sql = "update tbloja set posicao = ?, id_categoria = ?, telefone_contato = ?, cnpj = ?, loja_restaurante = ? where id = ?;";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $loja->getPosicao());
            $stmt->bindValue(2, $loja->getCategoria());
            $stmt->bindValue(3, $loja->getTelefoneContato());
            $stmt->bindValue(4, $loja->getCnpj());
            $stmt->bindValue(5, $loja->getTipoLoja()->value);
            $stmt->bindValue(6, $loja->getId());
            $stmt->execute();

            $horarioRepositorio = new HorarioFuncionamentoRepositorio($this->pdo);

            $sql = "delete from tbhorarioservico where id_servico = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $loja->getId());
            $stmt->execute();

            foreach ($loja->getHorarioFuncionamento() as $horario) {
                if (!$horarioRepositorio->verificarExistencia($horario)) {
                    $sql = "insert into tbhorariofuncionamento (horario_inicial, horario_final, dia_semana) values (?, ?, ?)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindValue(1, $horario->getHorarioInicial());
                    $stmt->bindValue(2, $horario->getHorarioFinal());
                    $stmt->bindValue(3, $horario->getDiaSemana());
                    $stmt->execute();
                }

                $sql = "insert into tbhorarioservico (horario_inicial, horario_final, dia_semana, id_servico) values (?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(1, $horario->getHorarioInicial());
                $stmt->bindValue(2, $horario->getHorarioFinal());
                $stmt->bindValue(3, $horario->getDiaSemana());
                $stmt->bindValue(4, $loja->getId());
                $stmt->execute();
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }


    public
    function excluirLoja(int $id): void
    {
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
        $sql = "SELECT tbServico.id, tbServico.nome, tbLoja.id_categoria, t2.horario_inicial, t2.horario_final, 
            tbLoja.cnpj, tbLoja.loja_restaurante, tbLoja.telefone_contato, tbServico.descricao, tbServico.imagem, tbServico.tipo_imagem, 
            tbLoja.posicao 
            FROM tbLoja 
            INNER JOIN tbServico ON tbLoja.id = tbServico.id 
            INNER JOIN dbshopping.tbHorarioServico t ON tbServico.id = t.id_servico
            INNER JOIN dbshopping.tbHorarioFuncionamento t2 ON t.horario_inicial = t2.horario_inicial AND t.horario_final = t2.horario_final AND t.dia_semana = t2.dia_semana
            ORDER BY tbLoja.id ASC";

        $result_set = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
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