<?php
$host = 'localhost';
$dbname = 'farmaciadb';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nome = $_POST['nome'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $telefone = $_POST['telefone'] ?? '';
    $endereco = $_POST['endereco'] ?? '';

    if (!empty($nome) && !empty($cpf)) {
        $sql = "INSERT INTO clientes (nome, cpf, telefone, endereco) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$nome, $cpf, $telefone, $endereco]);

        header("Location: clientes.php");
        exit;
    } else {
        $erro = "Nome e CPF são obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Cliente - Sistema Farmácia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="bg-light shadow-sm py-3">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <img src="../Imagens/farmacia.jpg" alt="logo" class="rounded-circle me-3" style="width: 65px; height: 65px;">
                <p class="fw-bold text-danger m-0">NOVO CLIENTE</p>
            </div>
            <a href="clientes.php" class="btn btn-danger rounded-pill fw-bold">Voltar</a>
        </div>
    </header>

    <div class="bg-dark" style="height: 8px;"></div>

    <main class="container my-5 flex-grow-1">
        <h1 class="text-center fw-bold mb-4" style="font-family: Georgia, serif;">Cadastrar Cliente</h1>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <form method="POST" class="mx-auto" style="max-width: 500px;">
            <div class="mb-3">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">CPF</label>
                <input type="text" name="cpf" id="cpf" class="form-control" placeholder="000.000.000-00" maxlength="14" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" id="telefone" class="form-control" placeholder="(XX) XXXXX-XXXX" maxlength="15">
            </div>
            <div class="mb-3">
                <label class="form-label">Endereço</label>
                <input type="text" name="endereco" class="form-control">
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-success px-4 fw-bold">Cadastrar</button>
            </div>
        </form>
    </main>

    <footer class="bg-dark text-white text-center py-3 mt-auto">
        <div class="container">
            &copy; Farmacia São João 2025. Todos os direitos reservados.
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        
        document.getElementById('cpf').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            e.target.value = value;
        });

        document.getElementById('telefone').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 11) value = value.slice(0, 11);
            if (value.length <= 10) {
                value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
            } else {
                value = value.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3');
            }
            e.target.value = value;
        });
    </script>
</body>
</html>
