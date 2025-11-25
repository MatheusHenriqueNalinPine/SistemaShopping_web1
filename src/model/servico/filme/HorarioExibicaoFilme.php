<?php

namespace model\servico\filme;
use DateTime;
use model\servico\filme\FormatoFilme;

require_once(__DIR__ . '/FormatoFilme.php');

class HorarioExibicaoFilme
{
    private int $idFilme;
    private DateTime $data_hora;
    private int $sala;
    private FormatoFilme $formato_filme;
    private string $modo_exibicao;

    /**
     * @param int $idFilme
     * @param DateTime $data_hora
     * @param int $sala
     * @param \model\servico\filme\FormatoFilme $formato_filme
     * @param string $modo_exibicao
     */
    public function __construct(int $idFilme, DateTime $data_hora, int $sala, \model\servico\filme\FormatoFilme $formato_filme, string $modo_exibicao)
    {
        $this->idFilme = $idFilme;
        $this->data_hora = $data_hora;
        $this->sala = $sala;
        $this->formato_filme = $formato_filme;
        $this->modo_exibicao = $modo_exibicao;
    }

    public function getIdFilme(): int
    {
        return $this->idFilme;
    }

    public function setIdFilme(int $idFilme): void
    {
        $this->idFilme = $idFilme;
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

    public function getFormatoFilme(): \model\servico\filme\FormatoFilme
    {
        return $this->formato_filme;
    }

    public function setFormatoFilme(\model\servico\filme\FormatoFilme $formato_filme): void
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