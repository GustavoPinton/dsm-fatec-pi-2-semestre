<?php
// Conexão com o banco
$host = "localhost";
$user = "root";
$pass = "";
$db = "farmaciadb"; // altere para o nome certo do seu banco

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}

// Buscar vendas + nome do cliente
$sql = "
SELECT v.id_venda, v.data, v.total, c.nome AS cliente
FROM vendas v
JOIN clientes c ON v.id_cliente = c.id_cliente
ORDER BY v.id_venda ASC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Vendas - Sistema Farmácia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/vendas.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<header class="bg-light shadow-sm py-3">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <img src="../Imagens/farmacia.jpg" alt="logo" class="rounded-circle me-3" style="width: 65px; height: 65px;">
            <p class="fw-bold text-danger m-0">CONTROLE DE VENDAS</p>
        </div>
        <nav class="d-flex gap-3">
            <a href="./login.php" class="btn btn-danger rounded-pill fw-bold">Sair</a>
            <a href="./nf.php" class="btn btn-danger rounded-pill fw-bold">NF</a>
            <a href="./estoque.php" class="btn btn-danger rounded-pill fw-bold">Estoque</a>
            <a href="./clientes.php" class="btn btn-danger rounded-pill fw-bold">Clientes</a>
            <a href="./vendas.php" class="btn btn-danger rounded-pill fw-bold">Vendas</a>
        </nav>
    </div>
</header>

<div class="bg-dark" style="height: 8px;"></div>

<main class="container my-5 flex-grow-1">
    <h1 class="text-center fw-bold mb-4" style="font-family: Georgia, serif;">Controle de Vendas</h1>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID Venda</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Total (R$)</th>
                    <th>Ações</th>

                </tr>
            </thead>
            <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($v = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?= $v['id_venda']; ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($v['data'])); ?></td>
                    <td><?= $v['cliente']; ?></td>
                    <td><?= number_format($v['total'], 2, ',', '.'); ?></td>
                    <td>
<form action="itensvenda.php" method="GET">
    <input type="hidden" name="id_venda" value="<?= $v['id_venda']; ?>">
    <button type="submit" style="padding:5px 10px; border-radius:5px; background:#007bff; color:white; border:none; cursor:pointer;">
        Ver Itens
    </button>
</form>
</td>


                </tr>
            <?php } } else { ?>
                <tr>
                  <td colspan="4" class="text-center text-danger fw-bold">Nenhuma venda cadastrada</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</main>

<footer class="bg-dark text-white text-center py-3 mt-auto">
    <div class="container">
        &copy; Farmacia São João 2025. Todos os direitos reservados.
    </div>
</footer>

</body>
</html>