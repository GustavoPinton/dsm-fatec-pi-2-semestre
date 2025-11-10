<?php
$host = 'localhost';
$dbname = 'farmaciadb';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados: " . $e->getMessage());
}

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = $_POST['preco'];
    $quantidade = $_POST['quantidade'];
    $validade = $_POST['validade'];

    if (empty($validade)) {
        $dias = rand(0, 365 * 5);
        $validade = date('Y-m-d', strtotime("+$dias days"));
    }

    if (!empty($nome) && is_numeric($preco) && is_numeric($quantidade)) {
        $stmt = $pdo->prepare("INSERT INTO medicamento (nome, preco, quantidade_estoque, data_validade)
                               VALUES (:nome, :preco, :quantidade, :validade)");
        $stmt->execute([
            ':nome' => $nome,
            ':preco' => $preco,
            ':quantidade' => $quantidade,
            ':validade' => $validade
        ]);
        $mensagem = "✅ Produto cadastrado com sucesso!";
    } else {
        $mensagem = "⚠️ Preencha todos os campos corretamente.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Produto - Sistema Farmácia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <header class="bg-dark text-white text-center py-3">
        <h2>Cadastro de Novo Produto</h2>
    </header>

    <main class="container my-5 flex-grow-1">
        <?php if ($mensagem): ?>
            <div class="alert alert-info text-center"><?= htmlspecialchars($mensagem) ?></div>
        <?php endif; ?>

        <form method="POST" class="p-4 bg-white rounded shadow-sm">
            <div class="mb-3">
                <label for="nome" class="form-label">Nome do Medicamento:</label>
                <input type="text" id="nome" name="nome" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="preco" class="form-label">Preço (R$):</label>
                <input type="number" step="0.01" id="preco" name="preco" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade em Estoque:</label>
                <input type="number" id="quantidade" name="quantidade" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="validade" class="form-label">Data de Validade (opcional):</label>
                <input type="date" id="validade" name="validade" class="form-control">
                
            </div>

            <div class="d-flex justify-content-between">
                <a href="estoque.php" class="btn btn-secondary">Voltar ao Estoque</a>
                <button type="submit" class="btn btn-success">Cadastrar Produto</button>
            </div>
        </form>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        &copy; Farmacia São João 2025. Todos os direitos reservados.
    </footer>
</body>
</html>
