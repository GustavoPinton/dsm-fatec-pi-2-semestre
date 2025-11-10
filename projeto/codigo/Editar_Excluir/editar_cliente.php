<?php
$host = 'localhost';
$dbname = 'farmaciadb';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id_cliente'];
    $nome = $_POST['nome'];
    $cpf = preg_replace('/\D/', '', $_POST['cpf']);
    $telefone = preg_replace('/\D/', '', $_POST['telefone']);
    $endereco = $_POST['endereco'];

    $stmt = $pdo->prepare("UPDATE clientes SET nome=?, cpf=?, telefone=?, endereco=? WHERE id_cliente=?");
    $stmt->execute([$nome, $cpf, $telefone, $endereco, $id]);
}