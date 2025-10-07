<?php

namespace model\repositorio;

use Cargo;
use PDO;
use Usuario;

require_once __DIR__ . '/../usuario/Usuario.php';
require_once __DIR__ . "/../usuario/Cargo.php";

class UsuarioRepositorio
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    private function formarObjeto(array $dados): Usuario
    {
        return new Usuario(
            $dados["id"] ?? null,
            $dados["nome"] ?? '',
            $dados["email"] ?? '',
            $dados["senha"] ?? '',
            $dados["cpf"] ?? '',
            Cargo::from($dados["cargo"]) ?? null);
    }

    public function buscarPorEmail($email): ?Usuario
    {
        $stmt = $this->pdo->prepare("select tbUsuario.id, tbUsuario.nome, tbUsuario.email, tbUsuario.senha, 
            tbUsuario.cpf, tbUsuario.cargo from tbUsuario where email = :email limit 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $dados = $stmt->fetch();
        return $dados ? $this->formarObjeto($dados) : null;
    }

    public function autenticar($email, $senha): bool
    {
        $usuario = $this->buscarPorEmail($email);
        return $usuario && password_verify($senha, $usuario->getSenha());
    }

    public function salvar(Usuario $usuario)
    {
        $sql = "insert into tbUsuario (nome, email, senha, cpf, cargo) values (?, ?, ?, ?, ?)";
        $stmt = $this->setarDadosStatement($sql, $usuario);
        $stmt->execute();
    }

    public function atualizar(Usuario $usuario)
    {
        $senha = $usuario->getSenha();

        if (!preg_match('/^\$2y\$/', $senha)) {
            $senha = password_hash($senha, PASSWORD_DEFAULT);
        }

        $sql = "update tbUsuario set nome = ?, email = ?, senha = ?, cpf = ?, cargo = ? where id = ?";
        $stmt = $this->setarDadosStatement($sql, $usuario);
        $stmt->bindValue(6, $usuario->getId());
        $stmt->execute();
    }

    public function excluir(int $id): bool
    {
        $sql = "delete from tbUsuario where id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function buscarTodos(): array
    {
        $sql = "select tbUsuario.id, tbUsuario.nome, tbUsuario.email, tbUsuario.senha, 
            tbUsuario.cpf, tbUsuario.cargo from usuarios order by email";
        $result_set = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($result) => $this->formarObjeto($result), $result_set);
    }

    private function setarDadosStatement(string $sql, Usuario $usuario): \PDOStatement|false
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(1, $usuario->getNome());
        $stmt->bindValue(2, $usuario->getEmail());
        $stmt->bindValue(3, password_hash($usuario->getSenha(), PASSWORD_DEFAULT));
        $stmt->bindValue(4, $usuario->getCpf());
        $stmt->bindValue(5, $usuario->getCargo());
        return $stmt;
    }

    public function cpfExists(string $cpf): bool {
        $stmt = $this->pdo->prepare("select cpf from tbusuario where cpf = ?");
        $stmt->execute([$cpf]);
        return $stmt->fetchColumn() > 0;
    }

    public function emailExists(string $email): bool {
        $sql = "select email from tbusuario where email = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }
}