<?php

require_once "conexao.php";


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $titulo = trim($_POST["titulo"]);
    $genero = trim($_POST["genero"]);
    $duracao = trim($_POST["duracao"]);
    $classificacao = trim($_POST["classificacao"]);
    $sinopse = trim($_POST["sinopse"]);

   
    if (empty($titulo) || empty($genero) || empty($duracao) || empty($classificacao)) {
        echo "Erro: Preencha todos os campos obrigatórios.";
        exit;
    }

  
    $sql = "INSERT INTO tbFilme (titulo, genero, duracao, classificacao, sinopse) 
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Erro na preparação da query: " . $conn->error);
    }

    $stmt->bind_param(
        "ssiss",
        $titulo,
        $genero,
        $duracao,
        $classificacao,
        $sinopse
    );

    if ($stmt->execute()) {
       
        header("Location: sucesso_filme.html");
        exit;
    } else {
        echo "Erro ao registrar o filme: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
