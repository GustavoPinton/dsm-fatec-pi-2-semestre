<?php
include("conexao.php");

header("Content-Type: application/json; charset=utf-8");

$busca = trim($_GET["q"] ?? "");

if ($busca === "") {
    echo json_encode([]);
    exit;
}

// Se for número, tentar buscar por ID ou código
if (is_numeric($busca)) {
    $sql = $pdo->prepare("
        SELECT * FROM produtos
        WHERE id = ? OR codigo_barras = ?
        LIMIT 10
    ");
    $sql->execute([$busca, $busca]);
} 
else {
    // Busca por nome (parcial)
    $sql = $pdo->prepare("
        SELECT * FROM produtos
        WHERE nome LIKE ?
        ORDER BY nome ASC
        LIMIT 10
    ");
    $sql->execute(["%$busca%"]);
}

$produtos = $sql->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($produtos);
?>
