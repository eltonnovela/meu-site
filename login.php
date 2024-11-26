<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/main.min.css">

    <!-- Fontes e Ícones -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;700&family=Oswald:wght@500&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="./assets/logo.png" type="image/x-icon">

    <title>Login - Lavandaria Expresso</title>
    <style>
        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Nunito', sans-serif;
        }
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login {
            display: flex;
            max-width: 900px;
            width: 100%;
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        /* Left Section */
        .imagem-login {
            flex: 1;
            background: url('./assets/laundry-background.jpg') no-repeat center center / cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 20px;
            color: #ffffff;
            text-align: center;
            animation: fadeIn 1.5s ease-in-out;
        }
        .imagem-login h2 {
            font-family: 'Oswald', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        .imagem-login h3 {
            font-size: 1.5rem;
        }

        /* Right Section */
        .informacoes-login {
            flex: 1;
            padding: 40px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: slideIn 1s ease-in-out;
        }
        .informacoes-login h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        .informacoes-login .mensagem {
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-bottom: 20px;
        }
        .infos {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }
        .input-group .icon {
            position: absolute;
            left: 10px;
            color: #7f8c8d;
            font-size: 1.2rem;
        }
        .input-group input {
            padding-left: 40px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
        }
        .input-group input:focus {
            border-color: #3498db;
            outline: none;
        }
        .sem-conta {
            margin-top: 15px;
            font-size: 0.9rem;
            color: #2c3e50;
        }
        .sem-conta a {
            color: #3498db;
            text-decoration: none;
            font-weight: 600;
        }
        .sem-conta a:hover {
            text-decoration: underline;
        }
        .entrar {
            margin-top: 20px;
            padding: 15px;
            background-color: #3498db;
            color: #ffffff;
            border: none;
            border-radius: 5px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }
        .entrar:hover {
            background-color: #2c3e50;
        }
        .entrar:active {
            transform: scale(0.95);
            background-color: #1d2a35;
        }

        /* Animações */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            0% {
                opacity: 0;
                transform: translateX(100px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
</head>
<body>
    <section class="login">
        <!-- Left Side -->
        <div class="imagem-login">
            <h2>Bem-vindo à nossa página!</h2>
            <h3>Lavandaria Expresso</h3>
        </div>

        <!-- Right Side -->
        <form action="./configLogin.php" method="POST" class="informacoes-login">
            <h2>LOGIN</h2>
            <h3 class="mensagem">Digite os seus dados para acessar o sistema. Caso não tenha uma conta, registre-se abaixo.</h3>
            <div class="infos">
                <div class="input-group">
                    <i class="icon fas fa-envelope"></i>
                    <input type="email" name="email" class="email" placeholder="Escreva seu e-mail" required>
                </div>
                <div class="input-group">
                    <i class="icon fas fa-lock"></i>
                    <input type="password" name="senha" class="senha" placeholder="Escreva sua senha" required>
                </div>
            </div>
            <h3 class="sem-conta">Não tem uma conta? <a href="./register.php">Registre-se</a></h3>
            <input type="submit" name="submit" value="Entrar" class="entrar">
        </form>
    </section>
</body>
</html>
