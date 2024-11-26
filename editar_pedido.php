<?php
include_once('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;
    $field = $_POST['field'] ?? null;
    $value = $_POST['value'] ?? null;

    if ($id && $field && $value) {
        $stmt = $conexao->prepare("UPDATE pedidos SET $field = ? WHERE id = ?");
        $stmt->bind_param('si', $value, $id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Alteração salva com sucesso!']);
        } else {
            echo json_encode(['success' => false, 'message' => $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Dados inválidos!']);
    }
}
?>
