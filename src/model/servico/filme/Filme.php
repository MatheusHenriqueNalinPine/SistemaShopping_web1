<?php

class Filme extends Servico
{
    private string $genero;
    private array $horario_exibicao_filme = [];

    public function __construct(string $nome, string $descricao, string $imagem, string $tipo_imagem,
                                Datetime $data_registro, string $genero, array $horario_exibicao_filme)
    {
        parent::__construct($nome, $descricao, $imagem, $tipo_imagem, $data_registro);
        $this->genero = $genero;
        $this->horario_exibicao_filme = $horario_exibicao_filme;
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