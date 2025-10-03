<?php

class HorarioExibicaoFilme
{
    private DateTime $data_hora;
    private int $sala;
    private FormatoFilme $formato_filme;
    private string $modo_exibicao;

    /**
     * @param DateTime $data_hora
     * @param int $sala
     * @param FormatoFilme $formato_filme
     * @param string $modo_exibicao
     */
    public function __construct(DateTime $data_hora, int $sala, FormatoFilme $formato_filme, string $modo_exibicao)
    {
        $this->data_hora = $data_hora;
        $this->sala = $sala;
        $this->formato_filme = $formato_filme;
        $this->modo_exibicao = $modo_exibicao;
    }

    public function getDataHora(): DateTime
    {
        return $this->data_hora;
    }

    public function setDataHora(DateTime $data_hora): void
    {
        $this->data_hora = $data_hora;
    }

    public function getSala(): int
    {
        return $this->sala;
    }

    public function setSala(int $sala): void
    {
        $this->sala = $sala;
    }

    public function getFormatoFilme(): FormatoFilme
    {
        return $this->formato_filme;
    }

    public function setFormatoFilme(FormatoFilme $formato_filme): void
    {
        $this->formato_filme = $formato_filme;
    }

    public function getModoExibicao(): string
    {
        return $this->modo_exibicao;
    }

    public function setModoExibicao(string $modo_exibicao): void
    {
        $this->modo_exibicao = $modo_exibicao;
    }
}