<?php
session_start();

// Verificar se o usuário está logado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Incluir a configuração do banco de dados
include_once('config.php');

// Obter dados do usuário logado
$usuario = $_SESSION['usuario'];
$sqlUsuario = "SELECT id, nome, email, foto FROM usuarios WHERE nome = ?";
$stmt = $conexao->prepare($sqlUsuario);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();
$usuarioInfo = $result->fetch_assoc();
$stmt->close();

// Verificar se o usuário foi encontrado
if (!$usuarioInfo) {
    echo "Usuário não encontrado no banco de dados.";
    exit();
}

// Pasta para armazenar as fotos de perfil
$pastaUploads = 'uploads/';
if (!is_dir($pastaUploads)) {
    mkdir($pastaUploads, 0755, true); // Criar a pasta se não existir
}

// Processar o formulário de edição
$mensagem = ''; // Variável para exibir mensagens ao usuário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $foto = $usuarioInfo['foto']; // Manter a foto atual por padrão

    // Verificação de campos obrigatórios
    if (!empty($nome) && !empty($email)) {
        // Verificar se foi feito um upload de foto
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];

            // Verificar se a extensão é válida
            if (in_array(strtolower($extensao), $tiposPermitidos)) {
                // Renomear o arquivo para evitar conflitos
                $novoNome = uniqid('foto_', true) . '.' . $extensao;
                $caminhoCompleto = $pastaUploads . $novoNome;

                // Fazer o upload do arquivo
                if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoCompleto)) {
                    $foto = $novoNome; // Atualiza o nome da foto no banco de dados
                } else {
                    $mensagem = "Erro ao fazer upload da foto.";
                }
            } else {
                $mensagem = "Tipo de arquivo inválido. Apenas JPG, JPEG, PNG e GIF são permitidos.";
            }
        }

        // Atualizar os dados no banco de dados
        $stmt = $conexao->prepare("UPDATE usuarios SET nome = ?, email = ?, foto = ? WHERE nome = ?");
        $stmt->bind_param("ssss", $nome, $email, $foto, $usuario);
        if ($stmt->execute()) {
            $_SESSION['usuario'] = $nome; // Atualiza o nome na sessão
            $_SESSION['foto'] = $foto; // Atualiza a foto na sessão
            header('Location: perfil.php'); // Redirecionar para o perfil após a atualização
            exit();
        } else {
            $mensagem = "Erro ao atualizar o perfil: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $mensagem = "Preencha todos os campos obrigatórios!";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Perfil</h2>

        <?php if (!empty($mensagem)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($mensagem); ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" name="nome" value="<?php echo htmlspecialchars($usuarioInfo['nome'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($usuarioInfo['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="foto" class="form-label">Foto de Perfil</label>
                <input type="file" class="form-control" name="foto">
                <small>Deixe em branco para manter a foto atual.</small>
            </div>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </form>

        <div class="mt-3">
            <a href="perfil.php" class="btn btn-secondary">Voltar ao Perfil</a>
            <a href="dashboard.php" class="btn btn-outline-dark">Voltar ao Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
