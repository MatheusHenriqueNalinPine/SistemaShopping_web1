<?php

namespace model\repositorio;

use DateTime;
use model\servico\filme\Filme;
use model\servico\filme\FormatoFilme;
use model\servico\filme\HorarioExibicaoFilme;
use PDO;

require_once(__DIR__ . '/../servico/filme/Filme.php');
require_once(__DIR__ . '/../servico/Servico.php');
require_once(__DIR__ . '/../servico/filme/FormatoFilme.php');
require_once(__DIR__ . '/../servico/filme/HorarioExibicaoFilme.php');

class HorarioFilmeRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function formarObjeto(array $dados)
    {
        return new HorarioExibicaoFilme($dados['id_filme'], new DateTime($dados['data_hora']), $dados['sala_filme'], FormatoFilme::from($dados['legendado_dublado']),
        $dados['modo_exibicao']);
    }

    public function salvar(int $id_filme, int $sala, FormatoFilme $formatoFilme, string $modo_exibicao, DateTime $dataFilme)
    {
        $sql = "insert into tbhorarioexibicaofilme (id_filme, data_hora, sala_filme, legendado_dublado,modo_exibicao) values (?, ?,?,?,?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $id_filme);
        $stmt->bindValue(2, $dataFilme->format('Y-m-d H:i:s'));
        $stmt->bindValue(3, $sala);
        $stmt->bindValue(4, $formatoFilme->value);
        $stmt->bindValue(5, $modo_exibicao);
        $stmt->execute();
    }

    public function atualizar(HorarioExibicaoFilme $horario)
    {
        $sql = "UPDATE tbHorarioExibicaoFilme 
            SET legendado_dublado = ?, modo_exibicao = ?
            WHERE id_filme = ? AND data_hora = ? AND sala_filme = ?";

        $stmt = $this->pdo->prepare($sql);

        $stmt->bindValue(1, $horario->getFormatoFilme()->value);
        $stmt->bindValue(2, $horario->getModoExibicao());
        $stmt->bindValue(3, $horario->getIdFilme());
        $stmt->bindValue(4, $horario->getDataHora()->format('Y-m-d H:i:s'));
        $stmt->bindValue(5, $horario->getSala());

        $stmt->execute();
        return true;
    }


    public function excluir(HorarioExibicaoFilme $horario)
    {
        $sql = "delete from tbhorarioexibicaofilme where id_filme = ? and  data_hora = ? and sala_filme = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $horario->getIdFilme());
        $stmt->bindValue(2, $horario->getDataHora()->format('Y-m-d H:i:s'));
        $stmt->bindValue(3, $horario->getSala());
        $stmt->execute();
        return true;
    }


    public function buscarPorChavePrimaria(int $idFilme, DateTime $dataHora, int $sala): ?HorarioExibicaoFilme
    {
        $sql = "SELECT * FROM tbHorarioExibicaoFilme 
                WHERE id_filme = ? AND data_hora = ? AND sala_filme = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $idFilme);
        $stmt->bindValue(2, $dataHora->format('Y-m-d H:i:s'));
        $stmt->bindValue(3, $sala);
        $stmt->execute();

        $dado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $dado ? $this->formarObjeto($dado) : null;
    }

    public function buscarPorIdFilme(int $idFilme): array
    {
        $sql = "SELECT * FROM tbHorarioExibicaoFilme WHERE id_filme = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $idFilme);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $horarios = [];
        foreach ($resultados as $dado) {
            $horarios[] = $this->formarObjeto($dado);
        }

        return $horarios;
    }


    public function buscarTodos(): array
    {
        $sql = "SELECT * FROM tbHorarioExibicaoFilme";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $horarios = [];
        foreach ($resultados as $dado) {
            $horarios[] = $this->formarObjeto($dado);
        }

        return $horarios;
    }


}