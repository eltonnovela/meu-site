<?php
session_start();
include_once('config.php'); // Inclui a configuração do banco de dados

if (isset($_POST['email']) && isset($_POST['senha'])) {
    $email = $_POST['email'];  // Não precisamos usar real_escape_string aqui, já que usaremos prepared statements
    $senha = $_POST['senha'];

    // Prepara a consulta SQL para buscar o usuário pelo e-mail
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conexao->prepare($sql);

    // Verifica se a preparação da consulta foi bem-sucedida
    if ($stmt === false) {
        die('Erro na preparação da consulta SQL: ' . $conexao->error);
    }

    // Vincula o parâmetro (e-mail) à consulta
    $stmt->bind_param("s", $email);

    // Executa a consulta
    $stmt->execute();
    $result = $stmt->get_result();

    // Verifica se o usuário foi encontrado
    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verifica se a senha fornecida corresponde à senha armazenada (usando password_verify)
        if (password_verify($senha, $usuario['senha'])) {
            // Login bem-sucedido, armazenando o ID do usuário e outros dados necessários na sessão
            $_SESSION['usuario_id'] = $usuario['id']; // Armazena o ID do usuário na sessão
            $_SESSION['usuario_nome'] = $usuario['nome']; // Armazena o nome do usuário na sessão
            $_SESSION['usuario_email'] = $usuario['email']; // Armazena o email do usuário

            // Redireciona para o dashboard ou perfil
            header('Location: dashboard.php'); // Redireciona para o dashboard (ou perfil)
            exit();
        } else {
            // Senha incorreta
            echo "<script>alert('Senha incorreta!'); window.location.href='login.php';</script>";
        }
    } else {
        // E-mail não encontrado
        echo "<script>alert('Email não encontrado!'); window.location.href='login.php';</script>";
    }

    // Fecha o statement
    $stmt->close();
} else {
    // Redireciona de volta para o login caso os dados não estejam presentes
    header('Location: login.php');
    exit();
}
?>

