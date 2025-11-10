<?php
header('Content-Type: application/json');

$host = 'localhost';
$dbname = 'farmaciadb';
$user = 'root';
$pass = '';

if (!isset($_POST['id_medicamento'])) {
    echo json_encode(['status'=>'error','mensagem'=>'ID do produto não informado']);
    exit;
}

$id = intval($_POST['id_medicamento']);

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Apenas marca como inativo
    $stmt = $pdo->prepare("UPDATE medicamento SET ativo = 0 WHERE id_medicamento = ?");
    $stmt->execute([$id]);

    if($stmt->rowCount() > 0){
        echo json_encode(['status'=>'success','mensagem'=>'Produto excluído com êxito!']);
    } else {
        echo json_encode(['status'=>'error','mensagem'=>'Produto não encontrado ou já excluído']);
    }
} catch (PDOException $e) {
    echo json_encode(['status'=>'error','mensagem'=>'Erro ao excluir: '.$e->getMessage()]);
}
