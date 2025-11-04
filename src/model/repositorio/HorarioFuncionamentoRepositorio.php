<?php

namespace model\repositorio;

use HorarioFuncionamento;
use PDO;

class HorarioFuncionamentoRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function salvar(HorarioFuncionamento $horario)
    {
        $sql = "insert into tbHorarioFuncionamento (dia_semana, horario_inicial, horario_final) values (?, ?, ?)";
        $this->executarSQL($sql, $horario);
    }

    public function atualizar(HorarioFuncionamento $horario)
    {
        try {

        } catch (\Exception $e) {
            $this->pdo->rollBack();
        }
    }

    public function excluir(HorarioFuncionamento $horario)
    {
        $sql = "delete from tbHorarioFuncionamento where horario_inicial = ? and horario_final = ? and dia_semana = ?";
        $this->executarSQL($sql, $horario);
    }

    public function verificarExistencia(HorarioFuncionamento $horario)
    {
        $sql = "select * from tbhorariofuncionamento where horario_inicial = ? and horario_final = ? and dia_semana = ?" .
            " limit 1";
        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            $horario->getHorarioInicial(),
            $horario->getHorarioFinal(),
            $horario->getDiaSemana()
        ]);

        return $stmt->fetch(PDO::FETCH_OBJ) !== false;
    }

    public function executarSQL(string $sql, HorarioFuncionamento $horario): void
    {
        $stmt = $this->pdo->prepare($sql);

        $diaSemana = $horario->getDiaSemana();
        $horarioInicial = $horario->getHorarioInicial();
        $horarioFinal = $horario->getHorarioFinal();

        $stmt->bindParam(1, $diaSemana);
        $stmt->bindParam(2, $horarioInicial);
        $stmt->bindParam(3, $horarioFinal);
        $stmt->execute();
    }
}