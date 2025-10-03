<?php

class HorarioFuncionamento
{
    private string $dia_semana;
    private string $horario_inicial;
    private string $horario_final;

    /**
     * @param string $dia_semana
     * @param string $horario_inicial
     * @param string $horario_final
     */
    public function __construct(string $dia_semana, string $horario_inicial, string $horario_final)
    {
        $this->dia_semana = $dia_semana;
        $this->horario_inicial = $horario_inicial;
        $this->horario_final = $horario_final;
    }

    public function getDiaSemana(): string
    {
        return $this->dia_semana;
    }

    public function setDiaSemana(string $dia_semana): void
    {
        $this->dia_semana = $dia_semana;
    }

    public function getHorarioInicial(): string
    {
        return $this->horario_inicial;
    }

    public function setHorarioInicial(string $horario_inicial): void
    {
        $this->horario_inicial = $horario_inicial;
    }

    public function getHorarioFinal(): string
    {
        return $this->horario_final;
    }

    public function setHorarioFinal(string $horario_final): void
    {
        $this->horario_final = $horario_final;
    }


}