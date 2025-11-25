<?php

namespace model\servico\filme;
use DateTime;
use model\servico\Servico;

require_once(__DIR__ . '/../../servico/Servico.php');

class Filme extends Servico
{
    private string $genero;
    private array $horario_exibicao_filme = [];

    public function __construct(int $id, string $nome, string $descricao, string $imagem, string $tipo_imagem, string $nome_imagem, string $url_imagem,
                                Datetime $data_registro, string $genero)
    {
        parent::__construct($id, $nome, $descricao, $imagem, $tipo_imagem, $nome_imagem, $url_imagem, $data_registro);
        $this->genero = $genero;
    }

    public function getGenero(): string
    {
        return $this->genero;
    }

    public function setGenero(string $genero): void
    {
        $this->genero = $genero;
    }

    public function getHorarioExibicaoFilme(): array
    {
        return $this->horario_exibicao_filme;
    }

    public function setHorarioExibicaoFilme(array $horario_exibicao_filme): void
    {
        $this->horario_exibicao_filme = $horario_exibicao_filme;
    }

}