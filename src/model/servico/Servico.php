<?php
/*Referências:

Imagens com MySQL: https://www.devmedia.com.br/armazenando-imagens-no-mysql/32104
Adicionar imagens do BD em PHP com string e tipo_imagem: chatGPT*/

namespace model\servico;

use DateTime;

abstract class Servico
{
    protected int $id;
    protected string $nome;
    protected string $descricao;
    protected string $imagem;
    protected string $tipoImagem;
    protected string $nomeImagem;
    protected string $urlImagem;
    protected DateTime $data_registro;

    public function __construct(int $id, string $nome, string $descricao, string $imagem, string $tipoImagem, string $nomeImagem, string $urlImagem, DateTime $data_registro)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->descricao = $descricao;
        $this->imagem = $imagem;
        $this->tipoImagem = $tipoImagem;
        $this->nomeImagem = $nomeImagem;
        $this->urlImagem = $urlImagem;
        $this->data_registro = $data_registro;
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

    public function getDataRegistro(): DateTime
    {
        return $this->data_registro;
    }

    public function setDataRegistro(DateTime $data_registro): void
    {
        $this->data_registro = $data_registro;
    }

    public function getTipoImagem(): string
    {
        return $this->tipoImagem;
    }

    public function setTipoImagem(string $tipoImagem): void
    {
        $this->tipoImagem = $tipoImagem;
    }

    public function getNomeImagem(): string
    {
        return $this->nomeImagem;
    }

    public function setNomeImagem(string $nomeImagem): void
    {
        $this->nomeImagem = $nomeImagem;
    }

    public function getUrlImagem(): string
    {
        return $this->urlImagem;
    }

    public function setUrlImagem(string $urlImagem): void
    {
        $this->urlImagem = $urlImagem;
    }
}
