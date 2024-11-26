<?php
session_start();
include_once('config.php'); // Conexão com o banco de dados

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Verifica se os dados foram enviados
if (isset($_POST['senha_atual'], $_POST['nova_senha'], $_POST['confirmar_senha'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $senha_atual = $_POST['senha_atual'];
    $nova_senha = $_POST['nova_senha'];
    $confirmar_senha = $_POST['confirmar_senha'];

    // Verifica se a nova senha e a confirmação são iguais
    if ($nova_senha !== $confirmar_senha) {
        echo "<script>alert('As novas senhas não coincidem!'); window.location.href='alterar_senha.php';</script>";
        exit();
    }

    // Busca a senha atual no banco de dados
    $sql = "SELECT senha FROM usuarios WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();

        // Verifica se a senha atual está correta
        if (password_verify($senha_atual, $usuario['senha'])) {
            // Atualiza a senha no banco de dados
            $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);
            $sql_update = "UPDATE usuarios SET senha = ? WHERE id = ?";
            $stmt_update = $conexao->prepare($sql_update);
            $stmt_update->bind_param("si", $nova_senha_hash, $usuario_id);

            if ($stmt_update->execute()) {
                echo "<script>alert('Senha alterada com sucesso!'); window.location.href='perfil.php';</script>";
            } else {
                echo "<script>alert('Erro ao alterar a senha!'); window.location.href='alterar_senha.php';</script>";
            }
        } else {
            echo "<script>alert('Senha atual incorreta!'); window.location.href='alterar_senha.php';</script>";
        }
    } else {
        echo "<script>alert('Usuário não encontrado!'); window.location.href='alterar_senha.php';</script>";
    }

    // Fecha as conexões
    $stmt->close();
    $conexao->close();
} else {
    header('Location: alterar_senha.php');
    exit();
}
?>
