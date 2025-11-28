<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "farmaciadb";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Dados recebidos do formulário
    $id_venda    = $_POST['id_venda'];
    $id_cliente  = $_POST['id_cliente'];
    $data        = $_POST['data'];
    $total       = $_POST['total'];

    // Verificar campos obrigatórios
    if(empty($id_venda) || empty($id_cliente) || empty($data) || empty($total)){
        throw new Exception("Dados incompletos.");
    }

    // Atualização
    $sql = "UPDATE vendas 
            SET id_cliente = :cliente, data = :data, total = :total
            WHERE id_venda = :id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":cliente", $id_cliente);
    $stmt->bindParam(":data", $data);
    $stmt->bindParam(":total", $total);
    $stmt->bindParam(":id", $id_venda);

    $stmt->execute();

    echo json_encode(["status" => "success", "mensagem" => "Venda atualizada com sucesso!"]);
    exit;

} catch (Exception $e) {

    echo json_encode(["status" => "error", "mensagem" => $e->getMessage()]);
    exit;
}
?>
