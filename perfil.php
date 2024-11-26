<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit();
}

// Dados da sessão
$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'];
$usuario_email = $_SESSION['usuario_email'];
$foto = $_SESSION['foto'] ?? 'default.jpg';

// Validação do arquivo da foto (verifica se existe e se é seguro)
$pastaUploads = 'uploads/';
$caminhoFoto = $pastaUploads . $foto;

if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $foto) || !file_exists($caminhoFoto)) {
    $caminhoFoto = $pastaUploads . 'default.jpg'; // Foto padrão caso o arquivo não exista
}

// Garante que a pasta uploads tenha uma foto padrão
if (!file_exists($pastaUploads . 'default.jpg')) {
    $caminhoFoto = 'https://via.placeholder.com/150'; // Imagem genérica online
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        .profile-card {
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            background-color: #f8f9fa;
        }
        .profile-card img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
        }
        .profile-card .card-title {
            font-weight: bold;
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card profile-card mx-auto" style="max-width: 400px;">
            <div class="card-body text-center">
                <!-- Exibição da foto de perfil -->
                <img src="<?php echo htmlspecialchars($caminhoFoto); ?>" alt="Foto de perfil">
                <h5 class="card-title mt-3"><?php echo htmlspecialchars($usuario_nome); ?></h5>
                <p class="card-text"><?php echo htmlspecialchars($usuario_email); ?></p>

                <!-- Links para editar perfil, alterar senha e voltar ao dashboard -->
                <a href="editar_perfil.php" class="btn btn-primary">Editar Perfil</a>
                <a href="alterar_senha.php" class="btn btn-secondary mt-2">Alterar Senha</a>
                <a href="dashboard.php" class="btn btn-outline-dark mt-3">Voltar ao Dashboard</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
