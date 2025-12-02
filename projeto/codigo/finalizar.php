<?php
include "conexao.php";

$data = json_decode(file_get_contents("php://input"), true);
$itens = $data["itens"];
$pagamento = $data["pagamento"];

$total = 0;

foreach ($itens as $item) {
    $total += $item["preco"] * $item["quantidade"];
}

$sql = $pdo->prepare("INSERT INTO vendas (total, metodo_pagamento) VALUES (?, ?)");
$sql->execute([$total, $pagamento]);

$venda_id = $pdo->lastInsertId();

foreach ($itens as $item) {
    $sql = $pdo->prepare("INSERT INTO venda_itens (venda_id, produto_id, quantidade, preco)
                          VALUES (?, ?, ?, ?)");
    $sql->execute([$venda_id, $item["id"], $item["quantidade"], $item["preco"]]);

    $sql = $pdo->prepare("UPDATE produtos SET estoque = estoque - ? WHERE id = ?");
    $sql->execute([$item["quantidade"], $item["id"]]);
}

echo json_encode(["status" => "ok", "venda_id" => $venda_id]);
?>
