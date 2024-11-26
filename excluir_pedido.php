<?php
// Incluir configuração do banco de dados
include_once('config.php');

// Obter o ID do pedido para exclusão
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'] ?? null;

if ($id) {
    // Preparar a query para excluir o pedido
    $stmt = $conexao->prepare("DELETE FROM pedidos WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Erro ao excluir pedido']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
}
?>
