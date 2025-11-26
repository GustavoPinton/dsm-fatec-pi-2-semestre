let carrinho = [];

const btnBuscar = document.getElementById("btnbuscar");

btnBuscar.addEventListener("click", buscar);  // <- CORRETO

function buscar() {
    let q = document.getElementById("busca").value;

    fetch("buscar.php?q=" + encodeURIComponent(q))
        .then(res => {
            if (!res.ok) throw new Error("Erro ao buscar");
            return res.json();
        })
        .then(produtos => {
            let lista = document.getElementById("lista-produtos");
            lista.innerHTML = "";

            if (produtos.length === 0) {
                lista.innerHTML = "<li>Nenhum produto encontrado.</li>";
                return;
            }

            produtos.forEach(p => {
                let li = document.createElement("li");
                li.innerHTML = `${p.nome} — R$ ${p.preco}`;
                li.onclick = () => adicionar(p);
                lista.appendChild(li);
            });
        })
        .catch(err => {
            alert("Erro ao buscar produtos");
            console.error(err);
        });
}

function adicionar(produto) {
    let item = carrinho.find(i => i.id === produto.id);

    if (item) {
        item.quantidade++;
    } else {
        carrinho.push({
            id: produto.id,
            nome: produto.nome,
            preco: produto.preco,
            quantidade: 1
        });
    }

    atualizarCarrinho();
}

function atualizarCarrinho() {
    let lista = document.getElementById("carrinho");
    lista.innerHTML = "";

    let total = 0;

    carrinho.forEach(item => {
        total += item.preco * item.quantidade;

        let li = document.createElement("li");
        li.innerHTML = `${item.nome} x${item.quantidade} — R$ ${(item.preco * item.quantidade).toFixed(2)}`;
        lista.appendChild(li);
    });

    document.getElementById("total").innerText = total.toFixed(2);
}

function finalizarVenda() {
    if (carrinho.length === 0) {
        alert("Carrinho vazio!");
        return;
    }

    fetch("finalizar.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"  // <- OBRIGATÓRIO
        },
        body: JSON.stringify({
            itens: carrinho,
            pagamento: "Dinheiro"
        })
    })
    .then(res => res.json())
    .then(r => {
        alert("Venda finalizada! Número: " + r.venda_id);
        carrinho = [];
        atualizarCarrinho();
    })
    .catch(err => {
        alert("Erro ao finalizar venda");
        console.error(err);
    });
}
