document.addEventListener("DOMContentLoaded", () => {

    const btnClienteNovo = document.getElementById('btn-Cliente-Novo');
    const customform = document.getElementById('customform');

    const btnNovoProduto = document.getElementById('btn-Novo-Produto');
    const customformProduto = document.getElementById('customform-Produto'); 

    if (btnClienteNovo && customform) {
        btnClienteNovo.addEventListener("click", () => {
            customform.classList.toggle('active');
        });
    }

    if (btnNovoProduto && customformProduto) {
        btnNovoProduto.addEventListener("click", () => {
            customformProduto.classList.toggle('active');
        });
    }

});
