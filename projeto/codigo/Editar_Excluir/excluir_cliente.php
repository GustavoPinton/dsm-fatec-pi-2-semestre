<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'farmaciadb';
$user = 'root';
$pass = '';

if (!isset($_POST['id_cliente'])) {
    echo json_encode(['status' => 'error', 'mensagem' => 'ID do cliente não informado']);
    exit;
}

$id = intval($_POST['id_cliente']);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("DELETE FROM clientes WHERE id_cliente = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'success', 'mensagem' => 'Cliente excluído com êxito!']);
    } else {
        echo json_encode(['status' => 'error', 'mensagem' => 'Cliente não encontrado ou já excluído']);
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'mensagem' => 'Erro ao excluir: ' . $e->getMessage()]);
}
