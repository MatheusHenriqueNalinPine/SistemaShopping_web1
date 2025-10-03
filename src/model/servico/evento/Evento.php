<?php

class Evento extends Servico
{
    private DateTime $data_inicial;
    private DateTime $data_final;
    private HorarioFuncionamento $horario_funcionamento;

    /**
     * @param DateTime $data_inicial
     * @param DateTime $data_final
     */
    public function __construct(string   $nome, string $descricao, string $imagem, string $tipo_imagem,
                                Datetime $data_registro, DateTime $data_inicial, DateTime $data_final,
                                HorarioFuncionamento $horario_funcionamento)
    {
        parent::__construct($nome, $descricao, $imagem, $tipo_imagem, $data_registro);
        $this->data_inicial = $data_inicial;
        $this->data_final = $data_final;
        $this->horario_funcionamento = $horario_funcionamento;
    }

    public function getDataInicial(): DateTime
    {
        return $this->data_inicial;
    }

    public function setDataInicial(DateTime $data_inicial): void
    {
        $this->data_inicial = $data_inicial;
    }

    public function getDataFinal(): DateTime
    {
        return $this->data_final;
    }

    public function setDataFinal(DateTime $data_final): void
    {
        $this->data_final = $data_final;
    }

    public function getHorarioFuncionamento(): HorarioFuncionamento
    {
        return $this->horario_funcionamento;
    }

    public function setHorarioFuncionamento(HorarioFuncionamento $horario_funcionamento): void
    {
        $this->horario_funcionamento = $horario_funcionamento;
    }


}