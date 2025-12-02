<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "farmaciadb";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) { die("Erro de conexão: " . $conn->connect_error); }

$sql = "
SELECT v.id_venda, v.data, v.total, v.id_cliente, c.nome AS cliente
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
<title>Vendas - Sistema Farmácia</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="../css/vendas.css" rel="stylesheet">
</head>
<body class="d-flex flex-column min-vh-100">

<header class="bg-light shadow-sm py-3">
<div class="container d-flex align-items-center justify-content-between">
<div class="d-flex align-items-center">
<img src="../Imagens/farmacia.jpg" style="width:65px;height:65px" class="rounded-circle me-3">
<p class="fw-bold text-danger m-0">CONTROLE DE VENDAS</p>
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

<div class="bg-dark" style="height:8px"></div>

<main class="container my-5 flex-grow-1">
<h1 class="text-center fw-bold mb-4" style="font-family: Georgia, serif;">Controle de Vendas</h1>

<div class="table-responsive">
<table class="table table-bordered table-striped align-middle text-center">
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
<?php if ($result->num_rows > 0) {
while ($v = $result->fetch_assoc()) { ?>
<tr id="venda-<?= $v['id_venda'] ?>">
<td><?= $v['id_venda'] ?></td>
<td><?= date('d/m/Y H:i', strtotime($v['data'])) ?></td>
<td><?= $v['cliente'] ?></td>
<td><?= number_format($v['total'], 2, ',', '.') ?></td>
<td class="d-flex gap-2 justify-content-center">

<form action="itensvenda.php" method="GET">
<input type="hidden" name="id_venda" value="<?= $v['id_venda'] ?>">
<button class="btn-acao">👁</button>
</form>

<button class="btn-acao btn-excluir" data-id="<?= $v['id_venda'] ?>">🗑</button>

</td>
</tr>
<?php }} else { ?>
<tr>
<td colspan="5" class="text-center text-danger fw-bold">Nenhuma venda cadastrada</td>
</tr>
<?php } ?>
</tbody>
</table>
</div>
</main>

<footer class="bg-dark text-white text-center py-3 mt-auto">
<div class="container">&copy; Farmacia São João 2025</div>
</footer>




<div id="mensagem" class="fade-message d-none"></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>


document.querySelectorAll('.btn-excluir').forEach(btn => {
btn.addEventListener('click', function(){
const id = this.getAttribute('data-id');
if(!confirm("Deseja excluir esta venda?")) return;
fetch("Editar_Excluir/excluir_venda.php", {
method: "POST",
headers: {"Content-Type":"application/x-www-form-urlencoded"},
body: "id_venda=" + id
})
.then(r=>r.json())
.then(d=>{
if(d.status === "success"){
document.getElementById("venda-"+id).remove();
mostrar("success", d.mensagem);
} else mostrar("error", d.mensagem);
});
});
});

function mostrar(tipo, texto){
const m = document.getElementById("mensagem");
m.className = "fade-message "+tipo;
m.textContent = texto;
m.classList.remove("d-none");
setTimeout(()=>m.style.opacity=0,2500);
setTimeout(()=>{m.classList.add("d-none");m.style.opacity=1;},3500);
}
</script>

</body>
</html>
