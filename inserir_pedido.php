<?php
// Iniciar sessão
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
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar e validar os dados recebidos do formulário
    $cliente = htmlspecialchars(trim($_POST['cliente']));
    $servico = htmlspecialchars(trim($_POST['servico']));
    $quantidade = intval($_POST['quantidade']);
    $material = htmlspecialchars(trim($_POST['material']));
    $status = htmlspecialchars(trim($_POST['status']));
    $data_pedido = htmlspecialchars(trim($_POST['data_pedido']));
    $data_entrega = htmlspecialchars(trim($_POST['data_entrega']));
    $observacoes = htmlspecialchars(trim($_POST['observacoes']));
    $valor_total = htmlspecialchars(trim($_POST['valor_total']));

    // Validar se os campos estão preenchidos e se valor_total é um número válido
    if (!empty($cliente) && !empty($servico) && $quantidade > 0 && !empty($material) && 
        !empty($status) && !empty($data_pedido) && !empty($data_entrega) && is_numeric($valor_total)) {

        // Preparar a consulta SQL para inserir o pedido no banco de dados
        $sqlInserirPedido = "INSERT INTO pedidos 
            (cliente, servico, quantidade, material, status, data_pedido, data_entrega, observacoes, valor_total) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        // Preparar a declaração
        if ($stmt = $conexao->prepare($sqlInserirPedido)) {
            // Bind dos parâmetros (s = string, i = integer, d = double)
            $stmt->bind_param('ssisisssd', $cliente, $servico, $quantidade, $material, $status, $data_pedido, $data_entrega, $observacoes, $valor_total);

            // Executar a consulta
            if ($stmt->execute()) {
                echo "Pedido inserido com sucesso!";
            } else {
                echo "Erro ao inserir pedido: " . $stmt->error;
            }

            // Fechar a declaração
            $stmt->close();
        } else {
            echo "Erro ao preparar a consulta: " . $conexao->error;
        }
    } else {
        echo "Por favor, preencha todos os campos corretamente.";
    }
}

// Fechar a conexão com o banco de dados
$conexao->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inserir Novo Pedido</title>
</head>
<body>
    <h1>Inserir Novo Pedido - Lavanderia</h1>
    <form action="inserir_pedido.php" method="POST">
        <label for="cliente">Cliente:</label><br>
        <input type="text" id="cliente" name="cliente" required><br><br>

        <label for="servico">Tipo de Serviço:</label><br>
        <select id="servico" name="servico" required>
            <option value="Lavagem">Lavagem</option>
            <option value="Passar Roupa">Passar Roupa</option>
            <option value="Lavagem a Seco">Lavagem a Seco</option>
        </select><br><br>

        <label for="quantidade">Quantidade de Peças:</label><br>
        <input type="number" id="quantidade" name="quantidade" min="1" required><br><br>

        <label for="material">Tipo de Material:</label><br>
        <input type="text" id="material" name="material" required><br><br>

        <label for="status">Status:</label><br>
        <input type="text" id="status" name="status" required><br><br>

        <label for="data_pedido">Data do Pedido:</label><br>
        <input type="date" id="data_pedido" name="data_pedido" required><br><br>

        <label for="data_entrega">Data de Entrega Estimada:</label><br>
        <input type="date" id="data_entrega" name="data_entrega" required><br><br>

        <label for="observacoes">Observações:</label><br>
        <textarea id="observacoes" name="observacoes" rows="4" cols="50"></textarea><br><br>

        <label for="valor_total">Valor Total:</label><br>
        <input type="number" id="valor_total" name="valor_total" step="0.01" required><br><br>

        <input type="submit" value="Inserir Pedido">
    </form>
</body>
</html>
