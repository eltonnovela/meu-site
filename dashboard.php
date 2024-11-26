<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Incluir a configuração do banco de dados
include_once('config.php');

// Verificar se a conexão foi estabelecida
if (!$conexao) {
    die("Falha na conexão com o banco de dados: " . $conexao->connect_error);
}

// Processar formulário de inserção
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cliente = $_POST['cliente'] ?? '';
    $servico = $_POST['servico'] ?? '';
    $quantidade = $_POST['quantidade'] ?? 0;
    $material = $_POST['material'] ?? '';
    $status = $_POST['status'] ?? '';
    $data_pedido = $_POST['data_pedido'] ?? '';
    $data_entrega = $_POST['data_entrega'] ?? '';
    $observacoes = $_POST['observacoes'] ?? '';
    $valor_total = $_POST['valor_total'] ?? 0.00;

    // Validação simples
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

// Obter o total de usuários registrados
$sqlTotalUsuarios = "SELECT COUNT(*) AS total FROM usuarios";
$resultTotalUsuarios = $conexao->query($sqlTotalUsuarios);
$totalUsuarios = $resultTotalUsuarios->fetch_assoc()['total'];

// Obter os últimos pedidos
$sqlUltimosPedidos = "SELECT id, cliente, servico, quantidade, material, status, data_pedido, data_entrega, valor_total 
                      FROM pedidos 
                      ORDER BY id ASC 
                      LIMIT 10";
$resultUltimosPedidos = $conexao->query($sqlUltimosPedidos);
$ultimosPedidos = $resultUltimosPedidos->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQ6Yq6ZBzoS+IiD0Bfu6EG69FoEOqMEyUqgzCIrErJ98hYZFAL1Zy2nQ+" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('caminho/para/sua/imagem/lavandaria.jpg'); /* Substitua pelo caminho da imagem */
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #fff;
        }

        .dashboard {
            margin-top: 30px;
        }

        .card {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            color: #333;
            font-weight: bold;
        }

        .table th {
            background-color: #007bff;
            color: #fff;
            text-align: center;
        }

        .table td {
            background-color: #f8f9fa;
            color: #333;
        }

        .btn-primary {
            background-color: #6f42c1;
            border-color: #6f42c1;
        }

        .btn-primary:hover {
            background-color: #5a2c92;
            border-color: #5a2c92;
        }

        .form-label {
            font-weight: bold;
            color: #333;
        }

        .navbar {
            background-color: #6f42c1;
        }

        .navbar-brand, .nav-link {
            color: #fff !important;
        }

        .navbar-nav .nav-item:hover {
            background-color: #5a2c92;
        }

        .alert {
            margin-top: 20px;
        }

        .excluir-pedido {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }

        .excluir-pedido:hover {
            background-color: #c82333;
        }

        .editable {
            background-color: #e9ecef;
            cursor: pointer;
        }

        .editable:focus {
            outline: none;
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Barra de navegação -->
        <nav class="navbar navbar-expand-lg navbar-light">
            <a class="navbar-brand" href="#">Dashboard</a>
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

        <!-- Bem-vindo -->
        <div class="text-center mb-4">
            <h1>Bem-vindo(a), <strong><?php echo htmlspecialchars($_SESSION['usuario']); ?></strong>!</h1>
        </div>

        <!-- Mensagem de feedback -->
        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-info"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <!-- Formulário para inserir pedido -->
        <div class="card bg-light mb-4">
            <div class="card-body">
                <h5 class="card-title">Adicionar Pedido</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label for="cliente" class="form-label">Cliente</label>
                        <input type="text" name="cliente" id="cliente" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="servico" class="form-label">Servico</label>
                        <input type="text" name="servico" id="servico" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="quantidade" class="form-label">Quantidade</label>
                        <input type="number" name="quantidade" id="quantidade" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="material" class="form-label">Material</label>
                        <input type="text" name="material" id="material" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <input type="text" name="status" id="status" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="data_pedido" class="form-label">Data do Pedido</label>
                        <input type="date" name="data_pedido" id="data_pedido" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="data_entrega" class="form-label">Data de Entrega</label>
                        <input type="date" name="data_entrega" id="data_entrega" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="observacoes" class="form-label">Observacoes</label>
                        <textarea name="observacoes" id="observacoes" class="form-control"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="valor_total" class="form-label">Valor Total</label>
                        <input type="number" step="0.01" name="valor_total" id="valor_total" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Adicionar</button>
                </form>
            </div>
        </div>

        <!-- Últimos Pedidos -->
        <div class="card bg-light mb-4">
      <div class="card-body">
    <h5 class="card-title">Ultimos Pedidos</h5>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Cliente</th>
                <th>Servico</th>
                <th>Quantidade</th>
                <th>Material</th>
                <th>Status</th>
                <th>Data do Pedido</th>
                <th>Data de Entrega</th>
                <th>Valor Total</th>
                <th>Acoes</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($ultimosPedidos as $pedido): ?>
                <tr>
                    <td><?php echo htmlspecialchars($pedido['id']); ?></td>
                    <td contenteditable="true" class="editable" data-field="cliente" data-id="<?php echo htmlspecialchars($pedido['id']); ?>" data-original-value="<?php echo htmlspecialchars($pedido['cliente']); ?>">
                        <?php echo htmlspecialchars($pedido['cliente']); ?>
                    </td>
                    <td contenteditable="true" class="editable" data-field="servico" data-id="<?php echo htmlspecialchars($pedido['id']); ?>" data-original-value="<?php echo htmlspecialchars($pedido['servico']); ?>">
                        <?php echo htmlspecialchars($pedido['servico']); ?>
                    </td>
                    <td contenteditable="true" class="editable" data-field="quantidade" data-id="<?php echo htmlspecialchars($pedido['id']); ?>" data-original-value="<?php echo htmlspecialchars($pedido['quantidade']); ?>">
                        <?php echo htmlspecialchars($pedido['quantidade']); ?>
                    </td>
                    <td contenteditable="true" class="editable" data-field="material" data-id="<?php echo htmlspecialchars($pedido['id']); ?>" data-original-value="<?php echo htmlspecialchars($pedido['material']); ?>">
                        <?php echo htmlspecialchars($pedido['material']); ?>
                    </td>
                    <td contenteditable="true" class="editable" data-field="status" data-id="<?php echo htmlspecialchars($pedido['id']); ?>" data-original-value="<?php echo htmlspecialchars($pedido['status']); ?>">
                        <?php echo htmlspecialchars($pedido['status']); ?>
                    </td>
                    <td><?php echo htmlspecialchars($pedido['data_pedido']); ?></td>
                    <td><?php echo htmlspecialchars($pedido['data_entrega']); ?></td>
                    <td contenteditable="true" class="editable" data-field="valor_total" data-id="<?php echo htmlspecialchars($pedido['id']); ?>" data-original-value="<?php echo htmlspecialchars($pedido['valor_total']); ?>">
                        <?php echo htmlspecialchars($pedido['valor_total']); ?>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm excluir-pedido" data-id="<?php echo htmlspecialchars($pedido['id']); ?>">Excluir</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <button class="btn btn-success" id="salvar-alteracoes">Salvar Alterações</button>
</div>

        </div>
    </div>
    <script src="script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-GLhlTQ8iRABwAXOSs5cH1L6jWb9u3QvWp3ujT6At9R2hZXW4Z+pN5r69ofsbHgM52" crossorigin="anonymous"></script>
    <script>
       document.addEventListener('DOMContentLoaded', function () {
    // Editar campos
    document.querySelectorAll('.editable').forEach(function (element) {
        element.addEventListener('blur', function () {
            const field = element.dataset.field;
            const value = element.textContent.trim(); // Remover espaços extras
            const id = element.dataset.id;

            // Verifica se o valor foi alterado
            if (value !== element.dataset.originalValue) {
                fetch('atualizar_pedido.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, field, value })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Edição salva:', data.message);
                        // Atualizar valor original após edição bem-sucedida
                        element.dataset.originalValue = value;
                    } else {
                        alert(data.message || 'Erro ao salvar edição.');
                        // Recarregar o valor original se houver erro
                        element.textContent = element.dataset.originalValue;
                    }
                })
                .catch(error => console.error('Erro ao salvar edição:', error));
            }
        });

        // Salvar o valor original ao focar no elemento
        element.addEventListener('focus', function () {
            element.dataset.originalValue = element.textContent.trim();
        });
    });

    // Salvar alterações em lote
    document.getElementById('salvar-alteracoes').addEventListener('click', function () {
        const edits = [];
        document.querySelectorAll('.editable').forEach(element => {
            const id = element.dataset.id;
            const field = element.dataset.field;
            const value = element.textContent.trim();
            const originalValue = element.dataset.originalValue;

            // Somente adiciona se o valor foi alterado
            if (value !== originalValue) {
                edits.push({ id, field, value });
            }
        });

        if (edits.length > 0) {
            fetch('atualizar_pedidos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(edits),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Alterações salvas com sucesso!');
                    location.reload();
                } else {
                    alert('Erro ao salvar alterações: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Erro ao salvar alterações:', error);
                alert('Erro ao salvar alterações.');
            });
        } else {
            alert('Nenhuma alteração detectada.');
        }
    });

    // Excluir pedido
    document.querySelectorAll('.excluir-pedido').forEach(function (button) {
        button.addEventListener('click', function () {
            const id = button.dataset.id;

            if (confirm('Tem certeza que deseja excluir este pedido?')) {
                fetch('excluir_pedido.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.closest('tr').remove();
                    } else {
                        alert(data.message || 'Erro ao excluir pedido.');
                    }
                })
                .catch(error => console.error('Erro ao excluir pedido:', error));
            }
        });
    });
});


    </script>
</body>
</html>
