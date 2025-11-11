<?php
require_once __DIR__ . '/config/config.php';

// Função para conectar ao banco de dados
function conectarDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }
    
    $conn->set_charset("utf8mb4");
    return $conn;
}

// Função para criar banco e tabela se não existirem
function inicializarDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS);
    
    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }
    
    // Criar banco de dados se não existir
    $sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $conn->query($sql);
    $conn->close();
    
    // Conectar ao banco criado
    $conn = conectarDB();
    
    // Criar tabela pedidos se não existir
    $sql = "CREATE TABLE IF NOT EXISTS pedidos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cliente VARCHAR(100) NOT NULL,
        tipo_material VARCHAR(100) NOT NULL,
        quantidade INT NOT NULL,
        observacao TEXT,
        status VARCHAR(30) NOT NULL DEFAULT 'Recebido',
        codigo_qr VARCHAR(255),
        data_cadastro DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conn->query($sql);
    
    return $conn;
}
?>

