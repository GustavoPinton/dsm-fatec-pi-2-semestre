<?php
$host = 'localhost';
$dbname = 'farmaciadb';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $id = $_POST['id_medicamento'];
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade_estoque'];
    $validade = $_POST['data_validade'];

    $sql = "UPDATE medicamento 
            SET nome = ?, preco = ?, quantidade_estoque = ?, data_validade = ?
            WHERE id_medicamento = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nome, $preco, $quantidade, $validade, $id]);

    header("Location: ../estoque.php?status=success");
    exit;
} catch (Exception $e) {
    header("Location: ../estoque.php?status=error");
    exit;
}
