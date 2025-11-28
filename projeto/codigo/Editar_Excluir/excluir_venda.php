<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "farmaciadb";

header("Content-Type: application/json");

try {
    if (!isset($_POST['id_venda'])) {
        throw new Exception("ID da venda não informado.");
    }

    $id_venda = (int) $_POST['id_venda'];

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Iniciar transação
    $pdo->beginTransaction();

    // 1 — Excluir itens da venda
    $stmt = $pdo->prepare("DELETE FROM itensvenda WHERE id_venda = :id");
    $stmt->bindParam(":id", $id_venda);
    $stmt->execute();

    // 2 — Excluir venda
    $stmt = $pdo->prepare("DELETE FROM vendas WHERE id_venda = :id");
    $stmt->bindParam(":id", $id_venda);
    $stmt->execute();

    $pdo->commit();

    echo json_encode(["status" => "success", "mensagem" => "Venda excluída com sucesso!"]);
    exit;

} catch (Exception $e) {

    echo json_encode(["status" => "error", "mensagem" => $e->getMessage()]);
    exit;
}
?>
