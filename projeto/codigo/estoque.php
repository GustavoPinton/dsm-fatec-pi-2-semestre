<?php
$host = 'localhost';
$dbname = 'farmaciadb';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query("SELECT * FROM medicamento WHERE ativo = 1");
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Estoque - Sistema Farmácia</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/estoque.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

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
            <a href="../codigo/vendas.php" class="btn btn-danger rounded-pill fw-bold">Vendas</a>
        </nav>
    </div>
</header>

<div class="bg-dark" style="height: 8px;"></div>

<main class="container my-5 flex-grow-1">
    <a href="novo_produto.php" class="btn btn-danger mb-4 fw-bold rounded-pill">Novo Produto</a>

    <h1 class="text-center fw-bold mb-4" style="font-family: Georgia, serif;">Estoque de Medicamentos</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Medicamento</th>
                    <th>Preço (R$)</th>
                    <th>Quantidade</th>
                    <th>Validade</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($produtos as $p): ?>
                <tr id="produto-<?= $p['id_medicamento'] ?>">
                    <td><?= $p['id_medicamento'] ?></td>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= number_format($p['preco'],2,',','.') ?></td>
                    <td><?= $p['quantidade_estoque'] ?></td>
                    <td><?= date('d/m/Y', strtotime($p['data_validade'])) ?></td>
                    <td>
                        <button class="btn btn-danger btn-sm fw-bold"
                                data-bs-toggle="modal" data-bs-target="#editarModal"
                                data-id="<?= $p['id_medicamento'] ?>"
                                data-nome="<?= htmlspecialchars($p['nome']) ?>"
                                data-preco="<?= $p['preco'] ?>"
                                data-quantidade="<?= $p['quantidade_estoque'] ?>"
                                data-validade="<?= $p['data_validade'] ?>">✎</button>
                        <button class="btn btn-danger btn-sm btn-excluir" data-id="<?= $p['id_medicamento'] ?>">🗑</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Modal de Edição -->
<div class="modal fade" id="editarModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="form-editar" action="Editar_Excluir/editar_produto.php" method="POST">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">Editar Produto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_medicamento" id="edit-id">
          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" id="edit-nome" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Preço (R$)</label>
            <input type="number" step="0.01" name="preco" id="edit-preco" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Quantidade</label>
            <input type="number" name="quantidade_estoque" id="edit-quantidade" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Validade</label>
            <input type="date" name="data_validade" id="edit-validade" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger fw-bold">Salvar Alterações</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Mensagem centralizada -->
<div id="mensagem" class="fade-message d-none"></div>

<footer class="bg-dark text-white text-center py-3 mt-auto">
    &copy; Farmacia São João 2025. Todos os direitos reservados.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Preenche modal
const editarModal = document.getElementById('editarModal');
editarModal.addEventListener('show.bs.modal', e => {
    const btn = e.relatedTarget;
    document.getElementById('edit-id').value = btn.getAttribute('data-id');
    document.getElementById('edit-nome').value = btn.getAttribute('data-nome');
    document.getElementById('edit-preco').value = btn.getAttribute('data-preco');
    document.getElementById('edit-quantidade').value = btn.getAttribute('data-quantidade');
    document.getElementById('edit-validade').value = btn.getAttribute('data-validade');
});

// Mensagem centralizada
function mostrarMensagem(tipo, texto){
    const msg = document.getElementById('mensagem');
    msg.className = 'fade-message ' + tipo;
    msg.textContent = texto;
    msg.style.opacity = 1;
    msg.classList.remove('d-none');
    setTimeout(() => { msg.style.opacity = 0; }, 3000);
    setTimeout(() => { msg.classList.add('d-none'); msg.style.opacity=1; }, 4000);
}

// Submit edição com toast
document.getElementById('form-editar').addEventListener('submit', function(e){
    e.preventDefault();
    mostrarMensagem('success','Alteração realizada com êxito!');
    setTimeout(() => { this.submit(); }, 500);
});

// Exclusão de produtos via AJAX
document.querySelectorAll('.btn-excluir').forEach(btn=>{
    btn.addEventListener('click', function(){
        const id = this.getAttribute('data-id');
        if(!confirm('Deseja realmente excluir este produto?')) return;

        fetch('Editar_Excluir/excluir_produto.php', {
            method:'POST',
            headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body:'id_medicamento='+encodeURIComponent(id)
        }).then(res=>res.json())
        .then(data=>{
            if(data.status==='success'){
                mostrarMensagem('success', data.mensagem);
                document.getElementById('produto-'+id).remove();
            } else {
                mostrarMensagem('error', data.mensagem || 'Erro ao excluir!');
            }
        }).catch(err=>{
            console.error(err);
            mostrarMensagem('error','Erro ao excluir!');
        });
    });
});
</script>
</body>
</html>
