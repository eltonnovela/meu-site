<?php
session_start();

// Verificar se o usuário está logado e tem privilégios de administrador
if (!isset($_SESSION['usuario']) || $_SESSION['usuario'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Incluir a configuração do banco de dados
include_once('config.php');

// Verificar se a conexão foi estabelecida
if (!$conexao) {
    die("Falha na conexão com o banco de dados: " . $conexao->connect_error);
}

// Processar formulário de inserção de pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add_pedido') {
        // Lógica para adicionar pedido (já fornecida anteriormente)
        $cliente = $_POST['cliente'] ?? '';
        $servico = $_POST['servico'] ?? '';
        $quantidade = $_POST['quantidade'] ?? 0;
        $material = $_POST['material'] ?? '';
        $status = $_POST['status'] ?? '';
        $data_pedido = $_POST['data_pedido'] ?? '';
        $data_entrega = $_POST['data_entrega'] ?? '';
        $observacoes = $_POST['observacoes'] ?? '';
        $valor_total = $_POST['valor_total'] ?? 0.00;

        // Validação e inserção do pedido no banco de dados
        if (!empty($cliente) && !empty($servico) && !empty($status) && !empty($data_pedido) && !empty($data_entrega)) {
            $stmt = $conexao->prepare("INSERT INTO pedidos (cliente, servico, quantidade, material, status, data_pedido, data_entrega, observacoes, valor_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssisssssd", $cliente, $servico, $quantidade, $material, $status, $data_pedido, $data_entrega, $observacoes, $valor_total);
            if ($stmt->execute()) {
                $mensagem = "Pedido adicionado com sucesso!";
            } else {
                $mensagem = "Erro ao adicionar pedido: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensagem = "Preencha todos os campos obrigatórios!";
        }
    }

    // Lógica para adicionar um novo usuário
    if ($_POST['action'] == 'add_usuario') {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $role = $_POST['role'] ?? 'usuario'; // 'usuario' ou 'admin'

        if (!empty($username) && !empty($password)) {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conexao->prepare("INSERT INTO usuarios (username, password, role) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $passwordHash, $role);
            if ($stmt->execute()) {
                $mensagem = "Usuário adicionado com sucesso!";
            } else {
                $mensagem = "Erro ao adicionar usuário: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensagem = "Preencha todos os campos obrigatórios!";
        }
    }

    // Lógica para excluir um usuário
    if ($_POST['action'] == 'delete_usuario') {
        $user_id = $_POST['user_id'] ?? 0;

        if ($user_id > 0) {
            $stmt = $conexao->prepare("DELETE FROM usuarios WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $mensagem = "Usuário excluído com sucesso!";
            } else {
                $mensagem = "Erro ao excluir usuário: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensagem = "Usuário inválido!";
        }
    }

    // Alterar status de um pedido
    if ($_POST['action'] == 'update_status') {
        $pedido_id = $_POST['pedido_id'] ?? 0;
        $status = $_POST['status'] ?? '';

        if ($pedido_id > 0 && !empty($status)) {
            $stmt = $conexao->prepare("UPDATE pedidos SET status = ? WHERE id = ?");
            $stmt->bind_param("si", $status, $pedido_id);
            if ($stmt->execute()) {
                $mensagem = "Status do pedido atualizado com sucesso!";
            } else {
                $mensagem = "Erro ao atualizar status: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $mensagem = "Selecione um pedido e um status válido!";
        }
    }
}

// Obter o total de usuários registrados
$sqlTotalUsuarios = "SELECT COUNT(*) AS total FROM usuarios";
$resultTotalUsuarios = $conexao->query($sqlTotalUsuarios);
$totalUsuarios = $resultTotalUsuarios->fetch_assoc()['total'];

// Obter os últimos pedidos
$sqlUltimosPedidos = "SELECT id, cliente, servico, quantidade, material, status, data_pedido, data_entrega, valor_total 
                      FROM pedidos 
                      ORDER BY id DESC 
                      LIMIT 10";
$resultUltimosPedidos = $conexao->query($sqlUltimosPedidos);
$ultimosPedidos = $resultUltimosPedidos->fetch_all(MYSQLI_ASSOC);

// Obter todos os usuários registrados
$sqlUsuarios = "SELECT id, username, role FROM usuarios";
$resultUsuarios = $conexao->query($sqlUsuarios);
$usuarios = $resultUsuarios->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .dashboard {
            margin-top: 30px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #007bff;
            color: #fff;
        }
        .table td {
            background-color: #f8f9fa;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
        .form-label {
            font-weight: bold;
        }
        .navbar {
            background-color: #6f42c1;
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="#">Dashboard Administrativo</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="sair.php">Sair</a>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="text-center mb-4">
            <h1>Bem-vindo(a), <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong></h1>
        </div>

        <!-- Mensagem de feedback -->
        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <!-- Adicionar Pedido -->
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Adicionar Pedido</h5>
                <form method="POST">
                    <input type="hidden" name="action" value="add_pedido">
                    <!-- Formulário de pedido (conforme código original) -->
                    <div class="mb-3">
                        <label for="cliente" class="form-label">Cliente</label>
                        <input type="text" name="cliente" id="cliente" class="form-control" required>
                    </div>
                    <!-- Continue com os outros campos do pedido -->
                    <button type
