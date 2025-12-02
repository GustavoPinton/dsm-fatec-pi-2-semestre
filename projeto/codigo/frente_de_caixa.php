<?php
session_start();
include("conexao.php");

// inicializa carrinho
if (!isset($_SESSION["carrinho"])) {
    $_SESSION["carrinho"] = [];
}

// FLASH message
function flash($msg, $tipo = "success") {
    $_SESSION["flash"] = [$msg, $tipo];
    header("Location: frente_de_caixa.php");
    exit;
}

// ----------------------------
// AÇÕES POST
// ----------------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $acao = $_POST["acao"] ?? "";

    // 1) ADICIONAR AO CARRINHO
    if ($acao === "adicionar") {

        $id = (int)$_POST["id_medicamento"];
        $qtd = max(1, (int)$_POST["qtd"]);

        $sql = $pdo->prepare("SELECT * FROM medicamento WHERE id_medicamento = ?");
        $sql->execute([$id]);
        $prod = $sql->fetch(PDO::FETCH_ASSOC);

        if (!$prod) {
            flash("Medicamento inexistente!", "danger");
        }

        // verificar estoque
        if ($qtd > $prod["quantidade_estoque"]) {
            flash("Quantidade maior do que o estoque!", "danger");
        }

        // adicionar ao carrinho
        $found = false;
        foreach ($_SESSION["carrinho"] as &$item) {
            if ($item["id_medicamento"] == $id) {
                $item["qtd"] += $qtd;
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $_SESSION["carrinho"][] = [
                "id_medicamento" => $prod["id_medicamento"],
                "nome" => $prod["nome"],
                "preco" => $prod["preco"],
                "qtd" => $qtd
            ];
        }

        flash("Medicamento adicionado!");
    }

    // 2) REMOVER
    if ($acao === "remover") {

        $id = (int)$_POST["id_medicamento"];

        foreach ($_SESSION["carrinho"] as $k => $item) {
            if ($item["id_medicamento"] == $id) {
                unset($_SESSION["carrinho"][$k]);
            }
        }

        $_SESSION["carrinho"] = array_values($_SESSION["carrinho"]);

        flash("Item removido.");
    }

    // 3) ATUALIZAR QUANTIDADE
    if ($acao === "atualizar") {

        $id = (int)$_POST["id_medicamento"];
        $qtd = max(1, (int)$_POST["qtd"]);

        foreach ($_SESSION["carrinho"] as &$item) {
            if ($item["id_medicamento"] == $id) {
                $item["qtd"] = $qtd;
            }
        }
        unset($item);

        flash("Quantidade atualizada!");
    }

    // 4) FINALIZAR VENDA
    if ($acao === "finalizar") {

        if (empty($_SESSION["carrinho"])) {
            flash("Carrinho vazio!", "danger");
        }

        $total = 0;
        foreach ($_SESSION["carrinho"] as $i) {
            $total += $i["preco"] * $i["qtd"];
        }

        // receber id_cliente do POST
        $id_cliente = (int)($_POST["id_cliente"] ?? 0);

        if ($id_cliente === 0) {
            flash("Selecione um cliente para finalizar a venda!", "danger");
        }

        // opcional: verificar se cliente existe (usar tabela 'clientes')
        $sqlCheckCliente = $pdo->prepare("SELECT nome FROM clientes WHERE id_cliente = ?");
        $sqlCheckCliente->execute([$id_cliente]);
        $clienteNome = $sqlCheckCliente->fetchColumn();

        if ($clienteNome === false) {
            flash("Cliente inválido!", "danger");
        }

        // inserir na tabela vendas
        $sql = $pdo->prepare(
            "INSERT INTO vendas (data, id_cliente, total) VALUES (NOW(), ?, ?)"
        );
        $sql->execute([$id_cliente, $total]);
        $idVenda = $pdo->lastInsertId();

        // inserir itens
        $sqlItem = $pdo->prepare(
            "INSERT INTO itensvenda (id_venda, id_medicamento, quantidade, preco_unitario)
             VALUES (?, ?, ?, ?)"
        );
        
        // atualizar estoque
        $sqlEstoque = $pdo->prepare(
            "UPDATE medicamento SET quantidade_estoque = quantidade_estoque - ?
             WHERE id_medicamento = ?"
        );

        foreach ($_SESSION["carrinho"] as $i) {
            $sqlItem->execute([$idVenda, $i["id_medicamento"], $i["qtd"], $i["preco"]]);
            $sqlEstoque->execute([$i["qtd"], $i["id_medicamento"]]);
        }

        $_SESSION["carrinho"] = [];

        flash("Venda finalizada! Nº $idVenda — Cliente: " . htmlspecialchars($clienteNome));
    }
}

// ----------------------------
// BUSCA DE MEDICAMENTOS
// ----------------------------
$busca = $_GET["q"] ?? "";

if ($busca != "") {
    $sql = $pdo->prepare("SELECT * FROM medicamento WHERE nome LIKE ? ORDER BY nome");
    $sql->execute(["%$busca%"]);
} else {
    $sql = $pdo->prepare("SELECT * FROM medicamento ORDER BY nome LIMIT 20");
    $sql->execute();
}

$produtos = $sql->fetchAll(PDO::FETCH_ASSOC);

// ----------------------------
// BUSCA DE CLIENTES (para o select) - usar tabela 'clientes'
// ----------------------------
$sqlClientes = $pdo->query("SELECT id_cliente, nome FROM clientes ORDER BY nome");
$clientes = $sqlClientes->fetchAll(PDO::FETCH_ASSOC);

// calcular total
$total = 0;
foreach ($_SESSION["carrinho"] as $i) {
    $total += $i["preco"] * $i["qtd"];
}
?>
//
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>PDV — Frente de Caixa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/frente_de_caixa.css">
</head>

<header class="bg-light shadow-sm py-3">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <img src="../Imagens/farmacia.jpg" alt="logo" class="rounded-circle me-3" style="width: 50px; height: 50px;">
            <p class="fw-bold text-danger m-0">ESTOQUE</p>
        </div>
        <nav class="d-flex gap-3">
            <a href="../codigo/login.html" class="btn btn-danger rounded-pill fw-bold">Sair</a>
            <a href="../codigo/nf.php" class="btn btn-danger rounded-pill fw-bold">NF</a>
            <a href="../codigo/estoque.php" class="btn btn-danger rounded-pill fw-bold">Estoque</a>
            <a href="../codigo/clientes.php" class="btn btn-danger rounded-pill fw-bold">Clientes</a>
            <a href="../codigo/frente_de_caixa.php" class="btn btn-danger rounded-pill fw-bold">PDV</a>
            <a href="../codigo/vendas.php" class="btn btn-danger rounded-pill fw-bold">Vendas</a>
        </nav>
    </div>
</header>

<body class="container py-4">

<h1 class="text-center mb-4">Frentes de Caixa (PDV)</h1>

<?php if (isset($_SESSION["flash"])): ?>
    <?php list($msg, $tipo) = $_SESSION["flash"]; ?>
    <div class="alert alert-<?= $tipo ?>"><?= $msg ?></div>
    <?php unset($_SESSION["flash"]); ?>
<?php endif; ?>

<div class="row">
    <!-- COLUNA PRODUTOS -->
    <div class="col-md-7">
        <h3>Buscar Medicamento</h3>
        <form method="GET" class="d-flex gap-2 mb-3">
            <input type="text" name="q" value="<?= htmlspecialchars($busca) ?>"
                   class="form-control" placeholder="Nome do medicamento...">
            <button class="btn btn-primary">Buscar</button>
        </form>

        <ul class="list-group">
            <?php foreach ($produtos as $p): ?>
                <li class="list-group-item d-flex justify-content-between">
                    <div>
                        <strong><?= $p["nome"] ?></strong><br>
                        R$ <?= number_format($p["preco"], 2, ',', '.') ?><br>
                        Estoque: <?= $p["quantidade_estoque"] ?>
                    </div>

                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="acao" value="adicionar">
                        <input type="hidden" name="id_medicamento" value="<?= $p["id_medicamento"] ?>">
                        <input type="number" name="qtd" value="1" min="1"
                               max="<?= $p["quantidade_estoque"] ?>"
                               class="form-control" style="width:80px">
                        <button class="btn btn-success">Adicionar</button>
                    </form>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <!-- COLUNA CARRINHO -->
    <div class="col-md-5">
        <h3>Carrinho</h3>

        <?php if (empty($_SESSION["carrinho"])): ?>
            <p class="text-muted">Carrinho vazio.</p>

        <?php else: ?>

        <ul class="list-group mb-3">
            <?php foreach ($_SESSION["carrinho"] as $i): ?>
                <li class="list-group-item">
                    <strong><?= $i["nome"] ?></strong><br>
                    R$ <?= number_format($i["preco"], 2, ',', '.') ?>
                    × <?= $i["qtd"] ?>
                    = <strong>R$ <?= number_format($i["preco"] * $i["qtd"], 2, ',', '.') ?></strong>

                    <div class="d-flex gap-2 mt-2">
                        <form method="POST" class="d-flex gap-2">
                            <input type="hidden" name="acao" value="atualizar">
                            <input type="hidden" name="id_medicamento" value="<?= $i["id_medicamento"] ?>">
                            <input class="form-control" type="number" name="qtd" 
                                   value="<?= $i["qtd"] ?>" min="1" style="width:80px">
                            <button class="btn btn-primary btn-sm">OK</button>
                        </form>

                        <form method="POST">
                            <input type="hidden" name="acao" value="remover">
                            <input type="hidden" name="id_medicamento" value="<?= $i["id_medicamento"] ?>">
                            <button class="btn btn-danger btn-sm">Remover</button>
                        </form>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

        <h4>Total: R$ <?= number_format($total, 2, ',', '.') ?></h4>

        <form method="POST">
            <label for="cliente" class="form-label">Cliente</label>
            <select name="id_cliente" class="form-select mb-3" required>
                <option value="">Selecione...</option>
                <?php foreach ($clientes as $c): ?>
                    <option value="<?= $c["id_cliente"] ?>">
                        <?= htmlspecialchars($c["nome"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="hidden" name="acao" value="finalizar">
            <button class="btn btn-success w-100">Finalizar Venda</button>
        </form>

        <?php endif; ?>
    </div>
</div>

</body>
</html>
