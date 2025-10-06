<?php

class Usuario
{
    private int $id;
    private string $nome;
    private string $email;
    private string $senha;
    private string $cpf;
    private Cargo $cargo;

    public function __construct(int $id, string $nome, string $email, string $senha, string $cpf, Cargo $cargo)
    {
        $this->id = $id;
        $this->nome = $nome;
        $this->email = $email;
        $this->senha = $senha;
        $this->cpf = $cpf;
        $this->cargo = $cargo;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getSenha(): string
    {
        return $this->senha;
    }

    public function setSenha(string $senha): void
    {
        $this->senha = $senha;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function setCpf(string $cpf): void
    {
        $this->cpf = $cpf;
    }

    public function getCargo(): Cargo
    {
        return $this->cargo;
    }

    public function setCargo(Cargo $cargo): void
    {
        $this->cargo = $cargo;
    }


}