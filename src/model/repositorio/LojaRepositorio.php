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
use model\repositorio\HorarioFuncionamentoRepositorio;

use DateTime;

require_once __DIR__ . "/../servico/loja/Loja.php";
require_once __DIR__ . "/../servico/loja/TipoLoja.php";
require_once __DIR__ . "/../servico/HorarioFuncionamento.php";
require_once __DIR__ . "/HorarioFuncionamentoRepositorio.php";

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

            $sql = "select id from tbCategoriaLoja where categoria = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $loja->getCategoria());
            $stmt->execute();
            $idCategoria = $stmt->fetchColumn();

            if (!$idCategoria) {
                $sql = "insert into tbCategoriaLoja (categoria) values (?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(1, $loja->getCategoria());
                $stmt->execute();
                $idCategoria = $this->pdo->lastInsertId();
            }

            $sql = "insert into tbloja (id, id_categoria ,posicao, telefone_contato, cnpj, loja_restaurante) values (?, ?, ?, ?, ?, ?);                                                                  ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $idServico);
            $stmt->bindValue(2, $idCategoria);
            $stmt->bindValue(3, $loja->getPosicao());
            $stmt->bindValue(4, $loja->getTelefoneContato());
            $stmt->bindValue(5, $loja->getCnpj());
            $stmt->bindValue(6, $loja->getTipoLoja()->value);
            $stmt->execute();

            $horarioRepositorio = new HorarioFuncionamentoRepositorio($this->pdo);

            foreach ($loja->getHorarioFuncionamento() as $horario) {
                if (!$horarioRepositorio->verificarExistencia($horario)) {
                    $sql = "insert into tbhorariofuncionamento (horario_inicial, horario_final, dia_semana)
                values (?, ?, ?)";
                    $stmt = $this->pdo->prepare($sql);
                    $stmt->bindValue(1, $horario->getHorarioInicial());
                    $stmt->bindValue(2, $horario->getHorarioFinal());
                    $stmt->bindValue(3, $horario->getDiaSemana());
                    $stmt->execute();
                }

                $sql = "insert into tbhorarioservico (horario_inicial, horario_final, dia_semana, id_servico)
            values (?, ?, ?, ?)";
                $stmt = $this->pdo->prepare($sql);
                $stmt->bindValue(1, $horario->getHorarioInicial());
                $stmt->bindValue(2, $horario->getHorarioFinal());
                $stmt->bindValue(3, $horario->getDiaSemana());
                $stmt->bindValue(4, $idServico);
                $stmt->execute();
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

            $sql = "update tbservico set nome = ?, descricao = ?, imagem = ?, tipo_imagem = ?, nome_imagem = ?, url_imagem = ? where id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $loja->getNome());
            $stmt->bindValue(2, $loja->getDescricao());
            $stmt->bindValue(3, $loja->getImagem());
            $stmt->bindValue(4, $loja->getTipoImagem());
            $stmt->bindValue(5, $loja->getNomeImagem());
            $stmt->bindValue(6, $loja->getUrlImagem());
            $stmt->bindValue(7, $loja->getId());
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


    public function excluirLoja(int $id): void
    {
        try {
            $this->pdo->beginTransaction();

            $sql = "DELETE FROM tbhorarioservico WHERE id_servico = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $id);
            $stmt->execute();

            $sql = "DELETE FROM tbloja WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $id);
            $stmt->execute();

            $sql = "DELETE FROM tbservico WHERE id = ?";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(1, $id);
            $stmt->execute();

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            echo "Erro ao salvar loja: " . $e->getMessage();
            throw $e;
        }
    }


    public function buscarlojas(): array
    {
        $sql = "select tbservico.id, tbservico.nome, tbloja.id_categoria, 
                   tbloja.cnpj, tbloja.loja_restaurante, tbloja.telefone_contato, 
                   tbservico.descricao, tbservico.imagem, tbservico.tipo_imagem, 
                   tbservico.nome_imagem, tbservico.url_imagem, tbservico.data_registro, 
                   tbloja.posicao 
            from tbloja 
            inner join tbservico on tbloja.id = tbservico.id 
            inner join tbCategoriaLoja on tbloja.id_categoria = tbCategoriaLoja.id 
            order by tbloja.id asc";

        $result_set = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    public function buscarlojasMinimizadas(int $limite): array
    {
        $sql = "select tbservico.id, tbservico.nome, tbloja.id_categoria, 
                   tbloja.cnpj, tbloja.loja_restaurante, tbloja.telefone_contato, 
                   tbservico.descricao, tbservico.imagem, tbservico.tipo_imagem, 
                   tbservico.nome_imagem, tbservico.url_imagem, tbservico.data_registro, 
                   tbloja.posicao 
            from tbloja 
            inner join tbservico on tbloja.id = tbservico.id 
            inner join tbCategoriaLoja on tbloja.id_categoria = tbCategoriaLoja.id 
            order by tbloja.id asc limit :limite";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();

        $result_set = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    public function buscarlojasFiltro(TipoLoja $tipo): array
    {
        $sql = "select tbservico.id, tbservico.nome, tbloja.id_categoria, 
                   tbloja.cnpj, tbloja.loja_restaurante, tbloja.telefone_contato, 
                   tbservico.descricao, tbservico.imagem, tbservico.tipo_imagem, 
                   tbservico.nome_imagem, tbservico.url_imagem, tbservico.data_registro, 
                   tbloja.posicao 
            from tbloja 
            inner join tbservico on tbloja.id = tbservico.id 
            inner join tbCategoriaLoja on tbloja.id_categoria = tbCategoriaLoja.id 
            where tbloja.loja_restaurante = :tipo
            order by tbloja.id asc";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['tipo' => $tipo->value]);
        $result_set = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    /**
     * @throws Exception
     */
    public function formarObjeto(array $result): Loja
    {
        return new Loja(
            $result['id'],
            $result['nome'] ?? null,
            $result['descricao'] ?? null,
            base64_encode($result['imagem'] !== null ? $result['imagem'] : ''),
            $result['tipo_imagem'] ?? 'image/png',
            $result['nome_imagem'] ?? '',
            $result['url_imagem'] ?? null,
            new DateTime($result['data_registro'] ?? 'now'),
            $result['posicao'] ?? null,
            $result['telefone_contato'] ?? null,
            $result['cnpj'] ?? null,
            $result['id_categoria'] ?? '0',
            TipoLoja::from($result['loja_restaurante']),
            $result['horarios'] ?? []
        );
    }

    /**
     * @throws Exception
     */
    public function buscarPorId(int $id): ?Loja
    {
        $sql = "select s.id, s.nome, l.id_categoria, c.categoria, l.cnpj, 
                       l.loja_restaurante, l.telefone_contato, s.descricao, 
                       s.imagem, s.nome_imagem, s.tipo_imagem, s.url_imagem, 
                       s.data_registro, l.posicao, hs.dia_semana, hs.horario_inicial,
                       hs.horario_final
                from tbLoja l
                inner join tbServico s on l.id = s.id
                inner join tbCategoriaLoja c on l.id_categoria = c.id
                inner join tbhorarioservico hs on hs.id_servico = l.id
                where s.id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return null;
        }

        $horarios = $this->buscarHorariosPorLojaId($id);
        $result['horarios'] = $horarios;

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

    private function buscarHorariosPorLojaId(int $id)
    {
        $sql = "select h.horario_inicial, h.horario_final, h.dia_semana from tbhorariofuncionamento h inner join tbhorarioservico hs on h. horario_inicial = hs.horario_inicial and h.horario_final = hs.horario_final and h.dia_semana = hs.dia_semana inner join dbshopping.tbloja l on hs.id_servico = l.id where l.id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $horarios = [];
        foreach ($result as $row) {
            $horarios[] = new HorarioFuncionamento(
                $row['horario_inicial'],
                $row['horario_final'],
                $row['dia_semana']
            );
        }

        return $horarios;
    }

    public function contarLojasPorTipo(TipoLoja $tipo): int
    {
        $sql = "select count(*) from tbloja where loja_restaurante = :tipo";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':tipo', $tipo->value);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }


    public function buscarlojasPaginadasPorTipo(TipoLoja $tipo, int $limite, int $offset): array
    {
        $sql = "select s.id, s.nome, l.id_categoria, c.categoria, 
                       l.cnpj, l.loja_restaurante, l.telefone_contato, 
                       s.descricao, s.imagem, s.tipo_imagem, 
                       s.nome_imagem, s.url_imagem, s.data_registro, 
                       l.posicao 
                from tbloja l 
                inner join tbservico s on l.id = s.id 
                inner join tbCategoriaLoja c on l.id_categoria = c.id 
                where l.loja_restaurante = :tipo
                order by s.data_registro desc 
                limit :limite offset :offset";

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':tipo', $tipo->value);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $result_set = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }
}