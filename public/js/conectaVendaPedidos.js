document.addEventListener('DOMContentLoaded', function () {


    const quantidadeInput = document.querySelector('#quantidade');
    const precoInput = document.querySelector('#valor_unitario');
    const subtotalInput = document.querySelector('#sub_total');

    // Função para calcular e atualizar o subtotal
    function calcularSubtotal() {
        // Obter os valores dos inputs. Use parseFloat para garantir que são números,
        // e trate casos onde o input pode estar vazio ou não ser um número.
        const quantidade = parseFloat(quantidadeInput.value) || 0;
        const preco = parseFloat(precoInput.value) || 0;

        const subtotal = quantidade * preco;

        // Atualizar o campo subtotal. Use toFixed(2) para formatar com 2 casas decimais.
        subtotalInput.value = subtotal.toFixed(2);
    }

    quantidadeInput.addEventListener('input', calcularSubtotal);
    precoInput.addEventListener('input', calcularSubtotal);

    calcularSubtotal();
});
