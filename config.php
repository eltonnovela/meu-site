<?php
// Configuração do banco de dados
$dbHost = 'localhost';
$dbUsername = 'root';
$dbPassword = '';
$dbName = 'formulario_teste';

// Estabelecer conexão inicial com o MySQL
$conexao = new mysqli($dbHost, $dbUsername, $dbPassword);

// Verifica conexão inicial
if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}

// Criar o banco de dados caso não exista
$sqlCreateDB = "CREATE DATABASE IF NOT EXISTS `$dbName`";
if (!$conexao->query($sqlCreateDB)) {
    die("Erro ao criar/verificar o banco de dados: " . $conexao->error);
}

// Selecionar o banco de dados
$conexao->select_db($dbName);

// Criar a tabela 'usuarios' caso não exista
$sqlCreateUsuarios = "
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL, -- Para armazenar senhas com hash
    telefone VARCHAR(150) NOT NULL UNIQUE,
    data_nasc DATE NOT NULL,
    genero VARCHAR(1) NOT NULL,
	foto VARCHAR(255) NULL,
	usuario VARCHAR(255) NOT NULL UNIQUE,
	tipo ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario'
)";
if (!$conexao->query($sqlCreateUsuarios)) {
    die("Erro ao criar/verificar a tabela 'usuarios': " . $conexao->error);
}

// Criar a tabela 'pedidos' caso não exista
$sqlCreatePedidos = "
CREATE TABLE IF NOT EXISTS pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cliente VARCHAR(150) NOT NULL,
    servico VARCHAR(100) NOT NULL,
    quantidade INT NOT NULL,
    material VARCHAR(100) NOT NULL,
    status VARCHAR(50) NOT NULL,
    data_pedido DATE NOT NULL,
    data_entrega DATE NOT NULL,
    observacoes TEXT,
    valor_total DECIMAL(10, 2) NOT NULL
)";
if (!$conexao->query($sqlCreatePedidos)) {
    die("Erro ao criar/verificar a tabela 'pedidos': " . $conexao->error);
}
?>
