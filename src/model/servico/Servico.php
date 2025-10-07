<?php

namespace model\servico;

abstract class Servico
{
    protected int $id;
    protected string $nome;
    protected string $descricao;
    protected string $imagem;
    protected string $tipo_imagem;
    protected DateTime $data_registro;

    public function __construct(int $id, string $nome, string $descricao, string $imagem, string $tipo_imagem)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->imagem = $imagem;
        $this->tipo_imagem = $tipo_imagem;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function setNome(string $nome): void
    {
        $this->nome = $nome;
    }

    public function getDescricao(): string
    {
        return $this->descricao;
    }

    public function setDescricao(string $descricao): void
    {
        $this->descricao = $descricao;
    }

    public function getImagem(): string
    {
        return $this->imagem;
    }

    public function setImagem(string $imagem): void
    {
        $this->imagem = $imagem;
    }

    public function getTipoImagem(): string
    {
        return $this->tipo_imagem;
    }

    public function setTipoImagem(string $tipo_imagem): void
    {
        $this->tipo_imagem = $tipo_imagem;
    }

    public function getDataRegistro(): DateTime
    {
        return $this->data_registro;
    }

    public function setDataRegistro(DateTime $data_registro): void
    {
        $this->data_registro = $data_registro;
    }
}