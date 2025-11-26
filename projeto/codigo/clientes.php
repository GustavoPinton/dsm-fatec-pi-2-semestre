<?php
$host = 'localhost';
$dbname = 'farmaciadb';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT * FROM clientes ORDER BY nome");
$clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Clientes - Sistema Farmácia</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../css/clientes.css">
<style>
body {
    background-image: url('../Imagens/login.jpg');
    background-size: cover;
    background-repeat: no-repeat;
    background-attachment: fixed;
}
</style>
</head>
<body class="d-flex flex-column min-vh-100">

<header class="bg-light shadow-sm py-3">
    <div class="container d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <img src="../Imagens/farmacia.jpg" alt="logo" class="rounded-circle me-3" style="width: 65px; height: 65px;">
            <p class="fw-bold text-danger m-0">CLIENTES</p>
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

<div class="bg-dark" style="height: 8px;"></div>

<main class="container my-5 flex-grow-1">
    <a href="cadastrocliente.php" class="btn btn-danger mb-4 fw-bold rounded-pill">Novo Cliente</a>

    <h1 class="text-center fw-bold mb-4" style="font-family: Georgia, serif;">Lista de Clientes</h1>
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Endereço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($clientes as $c): ?>
                    <tr id="cliente-<?= $c['id_cliente'] ?>">
                        <td><?= $c['id_cliente'] ?></td>
                        <td><?= htmlspecialchars($c['nome']) ?></td>
                        <td><?= preg_replace('/\D/', '', $c['cpf']) ?></td>
                        <td><?= preg_replace('/\D/', '', $c['telefone']) ?></td>
                        <td><?= htmlspecialchars($c['endereco']) ?></td>
                        <td>
                            <button 
                                class="btn btn-danger btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editarModal"
                                data-id="<?= $c['id_cliente'] ?>"
                                data-nome="<?= htmlspecialchars($c['nome']) ?>"
                                data-cpf="<?= preg_replace('/\D/', '', $c['cpf']) ?>"
                                data-telefone="<?= preg_replace('/\D/', '', $c['telefone']) ?>"
                                data-endereco="<?= htmlspecialchars($c['endereco']) ?>">
                                ✎
                            </button>
                            <button class="btn btn-danger btn-sm btn-excluir" data-id="<?= $c['id_cliente'] ?>">🗑</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<!-- Modal de Edição -->
<div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="form-editar" action="Editar_Excluir/editar_cliente.php" method="POST">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="editarModalLabel">Editar Cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id_cliente" id="edit-id">

          <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" id="edit-nome" class="form-control" required>
          </div>

          <div class="mb-3">
            <label class="form-label">CPF</label>
            <input type="text" name="cpf" id="edit-cpf" class="form-control" required maxlength="14" placeholder="Digite apenas números">
          </div>

          <div class="mb-3">
            <label class="form-label">Telefone</label>
            <input type="text" name="telefone" id="edit-telefone" class="form-control" required maxlength="15" placeholder="Digite apenas números">
          </div>

          <div class="mb-3">
            <label class="form-label">Endereço</label>
            <input type="text" name="endereco" id="edit-endereco" class="form-control" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Salvar Alterações</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Mensagem centralizada -->
<div id="mensagem" class="fade-message d-none"></div>

<footer class="bg-dark text-white text-center py-3 mt-auto">
    <div class="container">
        &copy; Farmacia São João 2025. Todos os direitos reservados.
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
<script>
const editarModal = document.getElementById('editarModal');

// Preenche modal de edição
editarModal.addEventListener('show.bs.modal', event => {
    const button = event.relatedTarget;
    document.getElementById('edit-id').value = button.getAttribute('data-id');
    document.getElementById('edit-nome').value = button.getAttribute('data-nome');
    document.getElementById('edit-cpf').value = button.getAttribute('data-cpf');
    document.getElementById('edit-telefone').value = button.getAttribute('data-telefone');
    document.getElementById('edit-endereco').value = button.getAttribute('data-endereco');
});

// Formata CPF enquanto digita
document.getElementById('edit-cpf').addEventListener('input', function(){
    let v = this.value.replace(/\D/g,'');
    if(v.length > 11) v = v.slice(0,11);
    if(v.length > 6) v = v.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
    else if(v.length > 3) v = v.replace(/(\d{3})(\d{1,3})/, '$1.$2');
    this.value = v;
});

// Formata telefone enquanto digita
document.getElementById('edit-telefone').addEventListener('input', function(){
    let v = this.value.replace(/\D/g,'');
    if(v.length > 11) v = v.slice(0,11);
    if(v.length > 10){
        v = v.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if(v.length > 5){
        v = v.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    }
    this.value = v;
});

// Função de mensagem centralizada
function mostrarMensagem(tipo, texto){
    const msg = document.getElementById('mensagem');
    msg.className = 'fade-message ' + tipo;
    msg.textContent = texto;
    msg.style.opacity = 1;
    msg.classList.remove('d-none');
    setTimeout(() => { msg.style.opacity = 0; }, 3000);
    setTimeout(() => { msg.classList.add('d-none'); msg.style.opacity = 1; }, 4000);
}

// Valida CPF e telefone
function validarCPF(cpf){
    cpf = cpf.replace(/\D/g,'');
    return cpf.length === 11;
}
function validarTelefone(telefone){
    telefone = telefone.replace(/\D/g,'');
    return telefone.length >= 10 && telefone.length <= 11;
}

// Submit do formulário de edição
document.getElementById('form-editar').addEventListener('submit', function(e){
    e.preventDefault();
    const cpf = document.getElementById('edit-cpf').value.replace(/\D/g,'');
    const telefone = document.getElementById('edit-telefone').value.replace(/\D/g,'');

    if(!validarCPF(cpf)){
        mostrarMensagem('error','CPF inválido!');
        return;
    }
    if(!validarTelefone(telefone)){
        mostrarMensagem('error','Telefone inválido!');
        return;
    }

    // Envia via fetch para evitar página em branco
    const formData = new FormData(this);
    fetch(this.action, {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(() => {
        mostrarMensagem('success','Alteração realizada com êxito!');
        setTimeout(() => { location.reload(); }, 500);
    })
    .catch(() => mostrarMensagem('error','Erro ao atualizar cliente!'));
});

// Exclusão de clientes
document.querySelectorAll('.btn-excluir').forEach(btn => {
    btn.addEventListener('click', function(){
        const id = this.getAttribute('data-id');
        if(!confirm('Deseja realmente excluir este cliente?')) return;

        fetch('Editar_Excluir/excluir_cliente.php', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: 'id_cliente=' + encodeURIComponent(id)
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success'){
                mostrarMensagem('success', data.mensagem);
                document.getElementById('cliente-' + id).remove();
            } else {
                mostrarMensagem('error', data.mensagem || 'Erro ao excluir!');
            }
        })
        .catch(err => {
            console.error(err);
            mostrarMensagem('error', 'Erro ao excluir!');
        });
    });
});
</script>

</body>
</html>
