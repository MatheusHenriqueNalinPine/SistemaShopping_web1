<?php

use model\servico\Servico;

require_once __DIR__ . "/../Servico.php";

class Loja extends Servico
{
    private string $posicao;
    private string $telefone_contato;
    private string $cnpj;
    private string $categoria;
    private TipoLoja $tipoLoja;
    private array $horarioFuncionamento;

    public function __construct(int      $id, string $nome, string $descricao, string $imagem, string $tipoImagem, string $nomeImagem, string $urlImagem, DateTime $data_registro, string $posicao, string $telefone_contato, string $cnpj, string $categoria,
                                TipoLoja $tipoLoja, array $horarioFuncionamento)
    {
        parent::__construct($id, $nome, $descricao, $imagem, $tipoImagem, $nomeImagem, $urlImagem, $data_registro);
        $this->posicao = $posicao;
        $this->telefone_contato = $telefone_contato;
        $this->cnpj = $cnpj;
        $this->categoria = $categoria;
        $this->tipoLoja = $tipoLoja;
        $this->horarioFuncionamento = $horarioFuncionamento;
    }

    public function getPosicao(): string
    {
        return $this->posicao;
    }

    public function setPosicao(string $posicao): void
    {
        $this->posicao = $posicao;
    }

    public function getTelefoneContato(): string
    {
        return $this->telefone_contato;
    }

    public function setTelefoneContato(string $telefone_contato): void
    {
        $this->telefone_contato = $telefone_contato;
    }

    public function getCnpj(): string
    {
        return $this->cnpj;
    }

    public function setCnpj(string $cnpj): void
    {
        $this->cnpj = $cnpj;
    }

    public function getCategoria(): string
    {
        return $this->categoria;
    }

    public function setCategoria(string $categoria): void
    {
        $this->categoria = $categoria;
    }

    public function getTipoLoja(): TipoLoja
    {
        return $this->tipoLoja;
    }

    public function setTipoLoja(TipoLoja $tipoLoja): void
    {
        $this->tipoLoja = $tipoLoja;
    }

    public function getHorarioFuncionamento(): array
    {
        return $this->horarioFuncionamento;
    }

    public function setHorarioFuncionamento(array $horarioFuncionamento): void
    {
        $this->horarioFuncionamento = $horarioFuncionamento;
    }

}