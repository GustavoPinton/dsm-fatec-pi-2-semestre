<?php
$host = "localhost";
$dbname = "farmaciadb";
$user = "root";
$pass = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
}

if (!isset($_GET['id_venda'])) {
    die("ID da venda não informado!");
}

$id_venda = $_GET['id_venda'];

$sql = "
SELECT iv.id_item, iv.quantidade, iv.preco_unitario, m.nome AS medicamento,
(iv.quantidade * iv.preco_unitario) AS total
FROM itensvenda iv
JOIN medicamento m ON iv.id_medicamento = m.id_medicamento
WHERE iv.id_venda = :id_venda
";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_venda', $id_venda);
$stmt->execute();
$itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Itens da Venda #<?= $id_venda ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">

    <h2 class="text-center mb-4">Itens da Venda #<?= $id_venda ?></h2>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID Item</th>
                <th>Medicamento</th>
                <th>Quantidade</th>
                <th>Preço Unitário</th>
                <th>Total Item</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($itens as $item): ?>
            <tr>
                <td><?= $item['id_item'] ?></td>
                <td><?= $item['medicamento'] ?></td>
                <td><?= $item['quantidade'] ?></td>
                <td>R$ <?= number_format($item['preco_unitario'], 2, ',', '.') ?></td>
                <td>R$ <?= number_format($item['total'], 2, ',', '.') ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <a href="./vendas.php" class="btn btn-secondary mt-3">⬅ Voltar</a>

</div>

</body>
</html>
