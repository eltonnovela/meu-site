<?php
include_once('config.php'); // Inclui a configuração do banco de dados

if (isset($_POST['submit'])) {
    // Sanitizar entradas do formulário
    $nome = $conexao->real_escape_string(trim($_POST['nome']));
    $email = $conexao->real_escape_string(trim($_POST['email']));
    $senha = $_POST['senha'];
    $senha_confirm = $_POST['senha_confirm'];
    $telefone = $conexao->real_escape_string(trim($_POST['telefone']));
    $nascimento = $conexao->real_escape_string($_POST['nascimento']);
    $genero = $conexao->real_escape_string($_POST['genero']);
    $tipo = $conexao->real_escape_string($_POST['tipo']); // Captura o tipo de usuário

    // Validações
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "Por favor, insira um e-mail válido.";
    } elseif ($senha !== $senha_confirm) {
        $erro = "As senhas não coincidem.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve conter pelo menos 6 caracteres.";
    } elseif (!in_array($tipo, ['usuario', 'admin'])) {
        $erro = "O tipo de conta selecionado é inválido.";
    } else {
        // Verifica se o e-mail ou telefone já estão registrados
        $sqlVerificar = "SELECT * FROM usuarios WHERE email = ? OR telefone = ?";
        $stmtVerificar = $conexao->prepare($sqlVerificar);
        $stmtVerificar->bind_param("ss", $email, $telefone);
        $stmtVerificar->execute();
        $resultVerificar = $stmtVerificar->get_result();

        if ($resultVerificar->num_rows > 0) {
            $erro = "Este e-mail ou telefone já está registrado.";
        } else {
            // Insere o novo usuário com tipo
            $sqlInserir = "INSERT INTO usuarios (nome, email, senha, telefone, data_nasc, genero, tipo) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmtInserir = $conexao->prepare($sqlInserir);
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT); // Criptografa a senha
            $stmtInserir->bind_param("sssssss", $nome, $email, $senha_hash, $telefone, $nascimento, $genero, $tipo);

            if ($stmtInserir->execute()) {
                header('Location: login.php');
                exit();
            } else {
                $erro = "Erro ao registrar. Tente novamente mais tarde.";
            }
        }
    }

    // Exibe a mensagem de erro, se houver
    if (isset($erro)) {
        echo "<script>alert('$erro'); window.location.href='register.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/main.min.css">
    <title>Registro - Lavandaria Expresso</title>
    <style>
        /* Estilos gerais */
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }

        .login {
            display: flex;
            min-height: 100vh;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #0099cc, #66ccff);
            padding: 20px;
        }

        .imagem-login {
            flex: 1;
            padding: 20px;
            text-align: center;
            background-image: url('https://media.gettyimages.com/id/1132394780/pt/foto/beautiful-woman-at-an-industrial-laundry.jpg?s=612x612');
            background-size: cover;
            background-position: center;
            color: white;
            border-radius: 10px;
            box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.3);
        }

        .imagem-login h2 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .imagem-login h3 {
            font-size: 1.5em;
            margin-bottom: 20px;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .informacoes-login {
            flex: 1;
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 100%;
        }

        .informacoes-login h2 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #333;
        }

        .informacoes-login .mensagem {
            font-size: 1.1em;
            margin-bottom: 30px;
            color: #555;
        }

        .informacoes-login .infos input,
        .informacoes-login input[type="submit"] {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1.1em;
        }

        .informacoes-login input[type="submit"] {
            background-color: #0099cc;
            color: white;
            cursor: pointer;
        }

        .informacoes-login input[type="submit"]:hover {
            background-color: #0077b3;
        }
    </style>
</head>
<body>
    <section class="login">
        <div class="imagem-login">
            <h2>Bem-vindo à Lavandaria Expresso</h2>
            <h3>O lugar perfeito para cuidar das suas roupas!</h3>
        </div>

        <div class="informacoes-login">
            <form action="register.php" method="post">
                <h2>Registrar-se</h2>
                <h3 class="mensagem">Preencha os dados abaixo para criar sua conta.</h3>
                <div class="infos">
                    <input type="text" name="nome" placeholder="Escreva seu nome" required>
                    <input type="email" name="email" placeholder="Escreva seu e-mail" required>
                    <input type="password" name="senha" placeholder="Escreva sua senha" required>
                    <input type="password" name="senha_confirm" placeholder="Confirme sua senha" required>
                    <input type="tel" name="telefone" placeholder="Escreva seu número de telefone" required>
                    <input type="date" name="nascimento" required>
                    <div class="sexo">
                        <p>Gênero:</p>
                        <label><input type="radio" name="genero" value="F" required> Feminino</label>
                        <label><input type="radio" name="genero" value="M" required> Masculino</label>
                        <label><input type="radio" name="genero" value="O" required> Outro</label>
                    </div>
                    <div class="tipo-usuario">
                        <p>Tipo de Conta:</p>
                        <label><input type="radio" name="tipo" value="usuario" required> Usuário</label>
                        <label><input type="radio" name="tipo" value="admin" required> Administrador</label>
                    </div>
                </div>
                <h3 class="sem-conta">Já tem uma conta? <a href="./login.php">Entrar</a></h3>
                <input type="submit" name="submit" value="Registrar">
            </form>
        </div>
    </section>
</body>
</html>
