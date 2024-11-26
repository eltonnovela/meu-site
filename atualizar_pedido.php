<?php
// Incluir a configuração do banco de dados
include_once('config.php');

// Recebe os dados em formato JSON
$data = json_decode(file_get_contents('php://input'), true);

// Verifica se os dados são de uma única atualização ou múltiplas
if (isset($data['id']) && isset($data['field']) && isset($data['value'])) {
    // Atualização de um único campo
    $id = $data['id'];
    $field = $data['field'];
    $value = $data['value'];

    // Validar o campo que está sendo atualizado
    if (in_array($field, ['cliente', 'servico', 'quantidade', 'material', 'status', 'data_pedido', 'data_entrega', 'valor_total'])) {
        $stmt = $conexao->prepare("UPDATE pedidos SET $field = ? WHERE id = ?");
        // Verifica o tipo do campo e ajusta a vinculação do parâmetro
        if ($field == 'quantidade' || $field == 'valor_total') {
            $stmt->bind_param('di', $value, $id); // Para valores numéricos (double ou integer)
        } else {
            $stmt->bind_param('si', $value, $id); // Para valores de string
        }

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Alteração salva com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao salvar alteração.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Campo inválido.']);
    }
} elseif (is_array($data)) {
    // Atualização de múltiplos campos
    foreach ($data as $edit) {
        $field = $edit['field'];
        $value = $edit['value'];
        $id = $edit['id'];
        
        // Validar o campo que está sendo atualizado
        if (in_array($field, ['cliente', 'servico', 'quantidade', 'material', 'status', 'data_pedido', 'data_entrega', 'valor_total'])) {
            $stmt = $conexao->prepare("UPDATE pedidos SET $field = ? WHERE id = ?");

            // Verifica o tipo do campo e ajusta a vinculação do parâmetro
            if ($field == 'quantidade' || $field == 'valor_total') {
                $stmt->bind_param('di', $value, $id); // Para valores numéricos (double ou integer)
            } else {
                $stmt->bind_param('si', $value, $id); // Para valores de string
            }

            // Executa a atualização do pedido
            if (!$stmt->execute()) {
                echo json_encode(['success' => false, 'message' => 'Erro ao salvar alteração no ID ' . $id]);
                $stmt->close();
                exit; // Se algum erro ocorrer, o script é interrompido
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Campo inválido no ID ' . $id]);
            exit;
        }
    }

    echo json_encode(['success' => true, 'message' => 'Alterações salvas com sucesso!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos.']);
}
?>
