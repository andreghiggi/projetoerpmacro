let scanner = null;
let modelo = null;

$(function(){
    modelo = $('#modelo').val()

    // $('#modalFrete').modal('show')
})

$("#btn-dark-mode").on("click", function () {

    $("body").toggleClass("dark-mode");

    if ($("body").hasClass("dark-mode")) {
        localStorage.setItem("pdv_dark", "1");

        $("#btn-dark-mode i")
        .removeClass("ri-moon-line")
        .addClass("ri-sun-line");
        $("#btn-dark-mode").contents().last()[0].textContent = " Modo Claro";

    } else {

        localStorage.setItem("pdv_dark", "0");

        $("#btn-dark-mode i")
        .removeClass("ri-sun-line")
        .addClass("ri-moon-line");

        $("#btn-dark-mode").contents().last()[0].textContent = " Modo Escuro";
    }
});

$(document).ready(function() {

    if (localStorage.getItem("pdv_dark") === "1") {
        document.body.classList.add("dark-mode");
        $("#btn-dark-mode i").removeClass("ri-moon-line").addClass("ri-sun-line");
        $("#btn-dark-mode").contents().last()[0].textContent = " Modo Claro";
    }

    $('.produto-item').show().addClass('show');

    $('#btn-menu').on('click', function() {
        $('#pdv-sidebar').addClass('open');
        $('.pdv-sidebar-overlay').fadeIn(150);
    });

    $('#close-sidebar').on('click', function() {
        $('#pdv-sidebar').removeClass('open');
        $('.pdv-sidebar-overlay').fadeOut(150);
    });

    $('.pdv-sidebar-overlay').on('click', function() {
        $('#pdv-sidebar').removeClass('open');
        $(this).fadeOut(150);
    });

    $('#btn-sangria').on('click', function() {
        $('#sangriaValor').val('');
        $('#sangriaObs').val('');
        $('#modalSangria').modal('show');
    });

    $('#btn-suprimento').on('click', function() {
        $('#suprimentoValor').val('');
        $('#suprimentoObs').val('');
        $('#modalSuprimento').modal('show');
    });

    $('#btn-comandas').on('click', function() {

        $('.loader').css('display', 'flex')
        $('.loader-text').text('Buscando comandas...')
        $.get(path_url + "api/pdv-mobo/comandas",
        {
            empresa_id: $('#empresa_id').val(),
        })
        .done((data) => {
            $('#modalComandas').modal('show');
            $('#modalComandas .modal-body').html(data);
            // console.log(data)
        })
        .fail((e) => {
            console.log(e);
            toastr.error("Não foi possivel buscar as comandas!")
        })
        .always(() => {
            $('.loader').css('display', 'none')
        });
    });

    $('#btn-vendas-diaria').on('click', function() {
        $('.loader').css('display', 'flex')
        $('.loader-text').text('Buscando vendas...')
        $.get(path_url + "api/pdv-mobo/vendas-diaria",
        {
            empresa_id: $('#empresa_id').val(),
            usuario_id: $('#usuario_id').val()
        })
        .done((data) => {
            $('#modalVendasDiaria').modal('show');
            $('#modalVendasDiaria #vendasDiaria').html(data);
        })
        .fail((e) => {
            console.log(e);
            toastr.error("Não foi possivel buscar as vendas do dia!")
        })
        .always(() => {
            $('.loader').css('display', 'none')
        });
    });

    $('#btn-vendas-suspensas').on('click', function() {
        $('.loader').css('display', 'flex')
        $('.loader-text').text('Buscando vendas...')
        $.get(path_url + "api/pdv-mobo/vendas-suspensa",
        {
            empresa_id: $('#empresa_id').val(),
        })
        .done((data) => {
            $('#modalVendasSuspensa').modal('show');
            $('#modalVendasSuspensa #vendasSuspensa').html(data);
        })
        .fail((e) => {
            console.log(e);
            toastr.error("Não foi possivel buscar as vendas suspensas!")
        }).always(() => {
            $('.loader').css('display', 'none')
        });
    });

    $('#btn-reiniciar').on('click', function() {
        swal({
            title: "Reiniciar",
            text: "Deseja realmente reiniciar a venda?",
            icon: "warning",
            buttons: true,
            buttons: ["Não", "Sim"],
            dangerMode: true,
        }).then((isConfirm) => {
            if (isConfirm) {
                resetForm()
                $('#pdv-sidebar').removeClass('open');
                $('.pdv-sidebar-overlay').fadeOut(150);
            }
        });
    });

    $('#btn-suspender').on('click', function() {

        let total = $('#btnTotal').text();
        if(convertMoedaToFloat(total) <= 0){
            toastr.error("Não é possível suspender sem nenhum item!");
            return;
        }
        swal({
            title: "Suspender",
            text: "Deseja realmente suspender a venda?",
            icon: "warning",
            buttons: true,
            buttons: ["Não", "Sim"],
            dangerMode: true,
        }).then((isConfirm) => {
            if (isConfirm) {
                suspender = 1
                salvarVenda()

                $('#pdv-sidebar').removeClass('open');
                $('.pdv-sidebar-overlay').fadeOut(150);
            }
        });
    });

    let hoje = new Date().toISOString().split("T")[0];
    $('#vencimentoForma').val(hoje);
});

$(document).on("focus", ".moeda", function () {
    $(this).mask("00000000,00", { reverse: true })
});

$(document).on("focus", ".peso", function () {
    $(this).mask("00000000.000", { reverse: true })
});

$(document).on("focus", ".placa", function () {
    $(this).mask("AAA-AAAA", { reverse: true })
});


$(".cpf_cnpj").inputmask({
  mask: ["999.999.999-99", "99.999.999/9999-99"], // CPF ou CNPJ
  keepStatic: true,    // escolhe automaticamente baseado no tamanho do valor
  showMaskOnHover: false,
  showMaskOnFocus: true,
  clearIncomplete: true // limpa se não digitar todos os dígitos
});

function convertMoedaToFloat(value) {
    if (!value) {
        return 0;
    }

    var number_without_mask = value.replaceAll(".", "").replaceAll(",", ".");
    return parseFloat(number_without_mask.replace(/[^0-9\.]+/g, ""));
}

function convertFloatToMoeda(value) {
    value = parseFloat(value)
    return value.toLocaleString("pt-BR", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

$('.pdv-cat').on('click', function() {
    $('.pdv-cat').removeClass('active')
    $(this).addClass('active')
    let cat = $(this).data('cat')
})

$(document).on("click", ".pdv-card", function () {
    let p = $(this).data('produto');
    abrirModalProduto(p);
})

$('.pdv-cat').on('click', function () {

    $('.pdv-cat').removeClass('active');
    $(this).addClass('active');

    let cat = $(this).data('cat');

    $('#pdvLoader').show();
    $('.row.g-1').html(""); // limpa a tela

    $.get(path_url + "api/pdv-mobo/produtos-categoria", {empresa_id: $('#empresa_id').val(), categoria_id: cat}, function(produtos) {

        $('#pdvLoader').hide();

        if(!produtos.length){
            $('.row.g-1').html('<div class="text-center text-muted mt-3">Nenhum produto encontrado</div>');
            return;
        }

        let html = "";
        produtos.forEach(p => {

            let img = p.imgApp;

            html += `
            <div class="col-4 produto-item show" data-cat="${p.categoria_id}">
            <div class="pdv-card" data-produto='${JSON.stringify(p)}'>
            <img src="${img}">
            <div class="pdv-card-title">${p.nome}</div>
            <div class="pdv-card-price">R$ ${ convertFloatToMoeda(p.valor_unitario)}</div>
            </div>
            </div>
            `;
        });

        $('.row.g-1').html(html);

        // animação suave
        setTimeout(() => {
            $('.produto-item').addClass('show');
        }, 30);

    }).fail(() => {
        toastr.error("Erro ao carregar produtos");
        $('#pdvLoader').hide();
    });
});

// FILTRAR POR NOME
$('#buscarProduto').on('keyup', function () {
    $('.pdv-cat').removeClass('active')
    $('.pdv-cat').first().addClass('active')
    let pesquisa = $(this).val().toLowerCase().trim();

    if(pesquisa){
        $.get(path_url + "api/pdv-mobo/produtos-categoria", {empresa_id: $('#empresa_id').val(), categoria_id: 0, pesquisa: pesquisa}, function(produtos) {

            $('#pdvLoader').hide();

            if(!produtos.length){
                $('.row.g-1').html('<div class="text-center text-muted mt-3">Nenhum produto encontrado</div>');
                return;
            }

            let html = "";
            produtos.forEach(p => {

                let img = p.imgApp;

                html += `
                <div class="col-4 produto-item show" data-cat="${p.categoria_id}">
                <div class="pdv-card" data-produto='${JSON.stringify(p)}'>
                <img src="${img}">
                <div class="pdv-card-title">${p.nome}</div>
                <div class="pdv-card-price">R$ ${ convertFloatToMoeda(p.valor_unitario)}</div>
                </div>
                </div>
                `;
            });

            $('.row.g-1').html(html);

            setTimeout(() => {
                $('.produto-item').addClass('show');
            }, 30);

        }).fail(() => {
            toastr.error("Erro ao carregar produtos");
            $('#pdvLoader').hide();
        });
    }
});

var carrinho = [];
$('#modalAdicionar').on('click', function () {

    let p = $(this).data('produto');

    let qtd = parseFloat($('#modalQtd').val());
    let valor = $('#modalValor').val().replace(',', '.');
    let observacao = $('#modalObservacao').val();

    if(produto != null && produto.categoria && produto.categoria.tipo_pizza == 1 && !tamanho_id){
        toastr.warning("Informe o tamanho para este produto!");
        $('#btnEscolherAdicionais').trigger('click')
        return;

    }
    // if(produto != null && produto.valor_com_adicional > 0){
    //     valor = produto.valor_com_adicional
    // }
    let item = {
        id: p.id,
        nome: p.nome,
        valor_unitario: parseFloat(valor),
        quantidade: qtd,
        observacao: observacao,
        subtotal: qtd * parseFloat(valor),
        referencia: p.referencia,
        codigo_barras: p.codigo_barras,
        categoria_id: p.categoria_id,
        img: p.imgApp,
        adicionais_escolhidos: produto != null ? produto.adicionais_escolhidos : [],
        adicionais_escolhidos_str: produto != null ? produto.adicionais_escolhidos_str : [],
        sabores_escolhidos: produto != null ? produto.sabores_escolhidos : [],
        sabores_escolhidos_str: produto != null ? produto.sabores_escolhidos_str : [],
        tamanho_id: tamanho_id,
    };

    carrinho.push(item);
    produto = null;

    atualizarBotaoCarrinho();
    $('#modalProduto').modal('hide');
    $('#buscarProduto').val('');
    $('.produto-item').show();
    setTimeout(() => {
        $('.produto-item').addClass('show');
    }, 10);

});

function atualizarBotaoCarrinho() {
    let totalItens = carrinho.reduce((sum, it) => sum + it.quantidade, 0);
    let totalGeral = carrinho.reduce((sum, it) => sum + parseFloat(it.subtotal), 0);
    $('#cartCount').text(totalItens);
    if (totalItens > 0) {

        $('#btnCarrinho').fadeIn(150);

        $('#cartCount').addClass('cart-pulse');
        setTimeout(() => {
            $('#cartCount').removeClass('cart-pulse');
        }, 1000);

    } else {
        $('#btnCarrinho').fadeOut(150);
    }
    $('#btnTotal').text("R$ " + convertFloatToMoeda(totalGeral));
}

$('#btnCarrinho').on('click', function () {
    montarCarrinho();
    $('#modalCarrinho').modal('show');
});

function montarCarrinho() {

    let html = '';

    carrinho.forEach((item, i) => {

        let adicionaisHtml = "";

        if (item.adicionais_escolhidos_str && item.adicionais_escolhidos_str.length > 0) {
            adicionaisHtml = `
            <div class="item-adicionais">
            <small class="text-muted">
            + ${item.adicionais_escolhidos_str.join(', ')}
            </small>
            </div>
            `;
        }

        let saboresHtml = "";

        if (item.sabores_escolhidos_str && item.sabores_escolhidos_str.length > 0) {
            saboresHtml = `
            <div class="item-adicionais">
            <small class="text-muted">
            - 1/${item.sabores_escolhidos_str.length} ${item.sabores_escolhidos_str.join(', ')}
            </small>
            </div>
            `;
        }

        html += `
        <div class="item-carrinho">

        <div class="item-left">
        <img src="${item.img ?? '/noimg.png'}" class="item-img">
        </div>

        <div class="item-info flex-grow-1">
        <strong>${item.nome}</strong><br>
        <small>${item.quantidade} × R$ ${ convertFloatToMoeda(item.valor_unitario) }</small>
        ${adicionaisHtml}
        ${saboresHtml}
        </div>

        <div class="text-end">
        <div class="item-valor">
        R$ ${ convertFloatToMoeda(item.subtotal) }
        </div>
        <button class="item-remove" onclick="removerItem(${i})">Remover</button>
        </div>

        </div>
        `;
    });

    $('#carrinhoLista').html(html);
}

$('#btnQtdMais').on('click', function() {
    let qtd = parseInt($('#modalQtd').val());
    $('#modalQtd').val(qtd + 1);
});

$('#btnQtdMenos').on('click', function() {
    let qtd = parseInt($('#modalQtd').val());
    if (qtd > 1) {
        $('#modalQtd').val(qtd - 1);
    }
});


function removerItem(index) {
    carrinho.splice(index, 1);
    montarCarrinho();
    atualizarBotaoCarrinho();
}

$('.escolherCliente').on('click', function () {
    $('#modalClientes').modal('show');
});

function montarListaClientes(filtro = "") {

    let html = "";

    clientes.forEach(c => {
        let nome = c.razao_social.toLowerCase();
        let doc = (c.cpf_cnpj ?? "").toLowerCase();

        if (nome.includes(filtro.toLowerCase()) || doc.includes(filtro.toLowerCase())) {

            html += `
            <div class="card-cliente">
            <div>
            <strong>${c.razao_social}</strong>
            <small>${c.cpf_cnpj ?? ''}</small>
            <small>${c.telefone ?? ''}</small>
            </div>
            <button class="cliente-btn-select" onclick="selecionarCliente(${c.id})">Selecionar</button>
            </div>
            `;
        }
    });

    $('#listaClientes').html(html);
}

$('#buscarCliente').on('keyup', function () {
    montarListaClientes($(this).val());
});

var clienteSelecinado = null
function selecionarCliente(id) {
    let cliente = clientes.find(c => c.id === id);

    clienteSelecinado = id
    $('#clienteNome').text(cliente.razao_social);
    $('#modalClientes').modal('hide');
}


let pagamentos = [];
let formaSelecionada = null;
// ABRIR MODAL
$('#btnTotal').click(function () {
    let total = $('#btnTotal').text();

    if(convertMoedaToFloat(total) <= 0){
        toastr.error("Adicione pelo menos 1 item para vender.");
        return;
    }

    if(modelo == 'pedido'){
        if(clienteSelecinado == null){
            toastr.error("Selecione o cliente para esta venda.");
            $('.escolherCliente').trigger('click')
            return;
        }
    }

    // zera pagamentos/inputs ao abrir
    pagamentos = [];
    formaSelecionada = null;
    $('#finalizarDesconto').val('');
    $('#valorForma').val('');
    $('#listaPagamentos').html('');

    $('#cpfNota').val('')

    if(clienteSelecinado){
        $('.div-cpf_cnpj').hide()
    }else{
        $('.div-cpf_cnpj').show()
    }

    $('#finalizarTotal').text(total);
    atualizarResumoPagamento();

    $('#modalFinalizar').modal('show');
});

function atualizarResumoPagamento() {
    let total = convertMoedaToFloat($('#btnTotal').text())

    let desconto = parseFloat($('#finalizarDesconto').val().replace('.', '').replace(',', '.') || 0);
    let acrescimo = parseFloat($('#finalizarAcrescimo').val().replace('.', '').replace(',', '.') || 0);
    let totalAPagar = total - desconto + acrescimo;

    if (totalAPagar < 0) totalAPagar = 0;

    let pago = pagamentos.reduce((s, p) => s + p.valor, 0);
    let falta = totalAPagar - pago;

    $('#resumoTotal').text("R$ " + totalAPagar.toFixed(2).replace('.', ','));
    $('#resumoPago').text("R$ " + pago.toFixed(2).replace('.', ','));
    $('#resumoFalta').text("R$ " + falta.toFixed(2).replace('.', ','));
}

$('.pg-btn').on('click', function() {
    $('.pg-btn').removeClass('pg-active');
    $(this).addClass('pg-active');
    formaSelecionada = $(this).data('pg');

    let nome = $(this).text().trim();
    $('#valorForma').attr('placeholder', "Valor em " + nome);
});

$('#btnAdicionarForma').on('click', function() {

    if (!formaSelecionada) {
        toastr.error("Selecione a forma de pagamento.");
        return;
    }

    let valorStr = convertMoedaToFloat($('#valorForma').val());
    let data = $('#vencimentoForma').val();
    let valor = parseFloat(valorStr || 0);

    if (isNaN(valor) || valor <= 0) {
        toastr.error("Informe um valor válido.");
        return;
    }

    pagamentos.push({
        forma: formaSelecionada,
        data: data,
        valor: valor
    });

    $('#valorForma').val('');
    renderizarPagamentos();
    atualizarResumoPagamento();
});

function renderizarPagamentos() {
    let html = '';

    pagamentos.forEach((pg, i) => {
        let label = '';

        switch(pg.forma) {
            case '01': label = 'Dinheiro'; break;
            case '02': label = 'Cheque'; break;
            case '03': label = 'Crédito'; break;
            case '04': label = 'Débito'; break;
            case '05': label = 'Crédito Loja'; break;
            case '06': label = 'Crediário'; break;
            case '10': label = 'Vale Alimentação'; break;
            case '11': label = 'Vale Refeição'; break;
            case '12': label = 'Vale Presente'; break;
            case '13': label = 'Vale Combustível'; break;
            case '14': label = 'Duplicata Mercantil'; break;
            case '15': label = 'Boleto Bancário'; break;
            case '16': label = 'Depósito Bancário'; break;
            case '17': label = 'PIX'; break;

            default: label = 'Outros'; break;
        }

        html += `
        <div class="linha-pagamento">
        <div>${label} ${ dataBR(pg.data) }</div>
        <div>
        R$ ${pg.valor.toFixed(2).replace('.', ',')}
        <button class="btn-remover-pg" onclick="removerPagamento(${i})">Remover</button>
        </div>
        </div>
        `;
    });

    $('#listaPagamentos').html(html);
}

function dataBR(dataISO) {
    if (!dataISO) return "";
    let partes = dataISO.split("-");
    return partes[2] + "/" + partes[1] + "/" + partes[0];
}

function removerPagamento(index) {
    pagamentos.splice(index, 1);
    renderizarPagamentos();
    atualizarResumoPagamento();
}

$('#finalizarDesconto').on('keyup change', function() {
    atualizarResumoPagamento();
});

$('#finalizarAcrescimo').on('keyup change', function() {
    atualizarResumoPagamento();
});

$('#finalizarRecebido').on('keyup', function () {
    let total = convertMoedaToFloat($('#btnTotal').text())
    let recebido = parseFloat($(this).val().replace(',', '.') || 0);
    let desconto = parseFloat($('#finalizarDesconto').val().replace(',', '.') || 0);

    let pagar = total - desconto;
    let troco = recebido - pagar;

    $('#finalizarTroco').text("R$ " + convertFloatToMoeda(troco));
});

var suspender = 0

$('#btnFinalizarVenda').click(function () {
    $('#modalCarrinho').modal('hide');
    $('#btnTotal').trigger('click')
})

$('#btnConfirmarVenda').click(function () {
    salvarVenda()
})

function salvarVenda(){

    let total = convertMoedaToFloat($('#btnTotal').text())
    let desconto = parseFloat($('#finalizarDesconto').val().replace('.', '').replace(',', '.') || 0);
    let acrescimo = parseFloat($('#finalizarAcrescimo').val().replace('.', '').replace(',', '.') || 0);
    let totalAPagar = total - desconto;
    if (totalAPagar < 0) totalAPagar = 0;

    let pago = pagamentos.reduce((s, p) => s + p.valor, 0);

    if (pagamentos.length === 0) {
        // toastr.error("Adicione ao menos uma forma de pagamento.");
        // return;
        pago = total
    }

    let troco = 0

    if(suspender == 0){

        if(formaSelecionada == null){
            toastr.error("Selecione o tipo de pagamento para esta venda.");
            return;
        }

        if (pago + 0.001 < totalAPagar) {
            toastr.error("Total pago menor que o valor da venda.");
            return;
        }
        let falta = parseFloat($('#resumoFalta').text().replace("R$", "").replace(",", ".").trim())
        if(falta < 0){
            troco = falta*-1
        }
    }

    let frete = null
    if($('#freteValor').val() != ''){
        frete = {
            valor: $('#freteValor').val(),
            qtd_volumes: $('#freteQtdVolumes').val(),
            numeracao_volumes: $('#freteNumVolumes').val(),
            peso_bruto: $('#fretePresoBruto').val(),
            peso_liquido: $('#fretePresoLiquido').val(),
            especie: $('#freteEspecie').val(),
            transportadora_id: $('#freteTransportadora').val(),
            tipo: $('#freteTipo').val(),
            placa: $('#fretePlaca').val(),
            uf: $('#freteUf').val(),
        }
    }

    let venda = {
        itens: carrinho,
        total: total,
        desconto: desconto,
        acrescimo: acrescimo,
        valor_total: totalAPagar,
        total_pago: pago,
        tipo_pagamento: formaSelecionada,
        troco: troco,
        fatura: pagamentos,
        cliente_id: clienteSelecinado,
        observacao: $('#finalizarObs').val(),
        data_atual: new Date().toLocaleString("pt-BR"),
        empresa_id: $('#empresa_id').val(),
        usuario_id: $('#usuario_id').val(),
        venda_suspensa_id: $('#venda_suspensa_id').val(),
        pedido_id: $('#pedido_id').val(),
        cliente_cpf_cnpj: $('#cpfNota').val()
    };

    // console.log("Venda Final:", venda);
    // return;

    let url = path_url + 'api/pdv-mobo/store'

    if(suspender == 1){
        url = path_url + 'api/pdv-mobo/suspender'
    }

    if(modelo == 'pedido'){
        url = path_url + 'api/pdv-mobo/store-nfe'
        $.post(url, venda)
        .done((success) => {
            console.log(success)
        }).fail((err) => {
            swal("Erro", "Algo deu errado ao salvar venda!", "error")
            console.log(err)
        });
    }else{

        $.post(url, venda)
        .done((success) => {

            if(suspender == 0){
                swal({
                    title: "Sucesso",
                    text: "Venda finalizada com sucesso, deseja emitir a NFCe?",
                    icon: "success",
                    buttons: true,
                    buttons: ["Não", "Sim"],
                    dangerMode: true,
                }).then((isConfirm) => {
                    if (isConfirm) {
                        gerarNfce(success)
                    }else{
                        resetForm()
                        $('#modalFinalizar').modal('hide');

                    }
                })
            }else{
                swal("Sucesso", "Venda suspensa", "success")
                .then(() => {
                    resetForm()
                })
            }
        }).fail((err) => {
            if(suspender == 1){
                swal("Erro", "Algo deu errado ao suspender venda!", "error")
            }else{
                swal("Erro", "Algo deu errado ao salvar venda!", "error")
            }
            console.log(err)
        });
    }
}

function gerarNfce(venda) {
    $('.loader').css('display', 'flex')
    $('.loader-text').text('Emitindo NFCe..')
    $.post(path_url + "api/nfce_painel/emitir", {
        id: venda.id,
    })
    .done((success) => {
        swal("Sucesso", "NFCe emitida " + success.recibo + " - chave: [" + success.chave + "]", "success")
        .then(() => {
            window.open(path_url + 'nfce/imprimir/' + venda.id, "_blank")
            setTimeout(() => {
                resetForm()
            }, 100)
        })
    })
    .fail((err) => {
        console.log(err)
        swal("Algo deu errado", err.responseJSON, "error")
    })
    .always(() => {
        $('.loader').css('display', 'none')
    });
}

function resetForm(){
    if ($('.comanda-tag').length > 0) {
        location.href = '/pdv-mobo'
    }
    $('.pg-btn').removeClass('pg-active');
    suspender = 0
    carrinho = []
    formaSelecionada = null

    atualizarBotaoCarrinho()

    $('#buscarProduto').val('');
    $('#buscarCliente').val('');
    $('.produto-item').show();
    setTimeout(() => {
        $('.produto-item').addClass('show');
    }, 10);

    $('.pdv-cat').first().trigger('click')
    $('#clienteNome').text('Cliente não selecionado');
    clienteSelecinado = null

    $('#cpfNota').val('');
    $('#finalizarDesconto').val('');
    $('#finalizarAcrescimo').val('');
    $('#finalizarObs').val('');

    $('#modalFrete').find('input').val('');
    $('#modalFrete').find('select').val('').change();
    
}

$('#btnConfirmarSangria').on('click', function () {

    let valor = convertMoedaToFloat($('#sangriaValor').val());
    let obs = $('#sangriaObs').val().trim();

    if (isNaN(valor) || valor <= 0) {
        toastr.error("Informe um valor válido para a sangria.");
        return;
    }

    let data = {
        empresa_id: $('#empresa_id').val(),
        usuario_id: $('#usuario_id').val(),
        valor: valor,
        observacao: obs
    };

    $.post(path_url + 'api/frenteCaixa/store-sangria', data)
    .done((success) => {
        toastr.success("Sangria realizada!")
        $('#modalSangria').modal('hide');
        $('#pdv-sidebar').removeClass('open');
        $('.pdv-sidebar-overlay').fadeOut(150);

    })
    .fail((err) => {
        toastr.error("Erro ao realizar sangria!")
    })
})

$('#btnConfirmarSuprimento').on('click', function () {

    let valor = convertMoedaToFloat($('#suprimentoValor').val());
    let obs = $('#suprimentoObs').val().trim();
    let tipo_pagamento = $('#suprimentoTipoPagamento').val();

    if (isNaN(valor) || valor <= 0 || !tipo_pagamento) {
        toastr.error("Informe um valor válido para o suprimento.");
        return;
    }

    let data = {
        empresa_id: $('#empresa_id').val(),
        usuario_id: $('#usuario_id').val(),
        valor: valor,
        tipo_pagamento: tipo_pagamento,
        observacao: obs
    };

    $.post(path_url + 'api/frenteCaixa/store-suprimento', data)
    .done((success) => {
        toastr.success("Suprimento realizado!")
        $('#modalSuprimento').modal('hide');
        $('#pdv-sidebar').removeClass('open');
        $('.pdv-sidebar-overlay').fadeOut(150);

    })
    .fail((err) => {
        toastr.error("Erro ao realizar suprimento!")
    })
})

$(document).on("click", ".cs-btn-remover", function () {

    let id = $(this).data("id");
    let card = $(this).closest(".card-suspensa");

    swal({
        title: "Remover venda?",
        text: "Essa ação não pode ser desfeita.",
        icon: "warning",
        buttons: ["Cancelar", "Remover"],
        dangerMode: true,
    })
    .then((confirmou) => {
        if (!confirmou) return;

        $.ajax({
            url: "/frontbox/destroy-suspensa/" + id,
            method: "GET",
            beforeSend: function () {
                // loader opcional
            }
        })
        .done(function () {

            // Remover o card da tela suavemente
            card.fadeOut(300, function () {
                $(this).remove();
            });

            toastr.success("Venda suspensa removida!");
        })
        .fail(function (err) {
            console.log(err);
            toastr.error("Erro ao remover venda.");
        });
    });
});

$('#btnScan').on('click', function () {

    $('#reader').show();
    $('.pdv-produtos-container').hide();
    $('.btn-close-scanner').removeClass('d-none')

    scanner = new Html5Qrcode("reader");

    scanner.start({ facingMode: "environment" },
    {
        fps: 10,
        qrbox: { width: 250, height: 120 }
    },
    decodedText => {

        $.get('/pdv-mobo/produtos-codigo-barras', {empresa_id: $('#empresa_id').val(), codigo_barras: decodedText})
        .done(function(produto) {

            beepSucesso();

            abrirModalProduto(produto);
        })
        .fail(function() {
            toastr.error("Produto não encontrado");
        });

    },
    errorMessage => {

    }).catch(err => {
        console.error(err);
        toastr.error("Não foi possível acessar a câmera");
        fecharScanner();
    });
});

function beepSucesso(){
    var audio = new Audio('/audio/beep.mp3');
    audio.addEventListener('canplaythrough', function() {
        audio.play();
    });
}

function fecharScanner() {
    if (scanner) {
        scanner.stop().then(() => {
            scanner.clear();
        });
    }

    $('#reader').hide();
    $('.pdv-produtos-container').show();
}


$(document).on("click", "#btnCloseScanner", function () {
    fecharScanner();
    $('.btn-close-scanner').addClass('d-none')
});

var produto = null
var tamanho_id = null
var adicionaisEscolhidos = []
var saboresEscolhidos = []
var saboresEscolhidosStr = []
function abrirModalProduto(p) {
    // console.log(p)
    $('#btnEscolherAdicionais').addClass('d-none')

    $('#modalNome').val(p.nome);
    $('#modalValor').val(convertFloatToMoeda(p.valor_unitario))
    $('#modalQtd').val(1);
    $('#modalObservacao').val('')
    $('#modalImagem').attr('src', p.imgApp ?? '/noimg.png');

    if (p.gerenciar_estoque == 1) {
        $('.estoque-box').show();
        $('#modalEstoque').text(parseInt(p.estoque_atual));
    } else {
        $('.estoque-box').hide();
    }

    if(p.categoria && p.categoria.tipo_pizza == 1){
        $('#btnEscolherAdicionais').text('Escolher Adicionais/Sabores')
        $('#modalValor').val('')
        $('#modalValor').prop('disabled', 1)
    }else{
        $('#btnEscolherAdicionais').text('Escolher Adicionais')
        $('#modalValor').prop('disabled', 0)
    }

    if(p.adicionais && p.adicionais.length > 0){
        produto = p
        adicionaisEscolhidos = []

        produto.valor_com_adicional = 0
        produto.adicionais_escolhidos = []
        produto.adicionais_escolhidos_str = []
        produto.sabores_escolhidos = []
        produto.sabores_escolhidos_str = []
        $('#btnEscolherAdicionais').removeClass('d-none')
    }
    $('#modalAdicionar').data('produto', p);
    $('#modalProduto').modal('show');
}

$('#btnEscolherAdicionais').on('click', function () {
    $('#modalProduto').modal('hide');

    setTimeout(() => {
        $('#modalEscolherAdicionais').modal('show');
        $('#modalEscolherAdicionais .modal-title').text(`Adicionais ${produto.nome}`);
        $('#modalEscolherAdicionais .valor_unitario').html(`Valor unitário: <strong>R$ ${convertFloatToMoeda(produto.valor_unitario)}</strong>`);
        $('#modalEscolherAdicionais #totalAdicionais').text(convertFloatToMoeda(produto.valor_unitario));
        let html = '';

        produto.adicionais.forEach(item => {

            let checked = "";
            if (produto.adicionais_escolhidos.includes(item.adicional.id)) {
                checked = "checked";
            }
            html += `
            <label class="custom-checkbox">
            <input ${checked} type="checkbox" class="chk-adicional" value="${item.adicional.id}" data-valor="${item.adicional.valor}">
            <b>${item.adicional.nome}</b>
            <span class="ms-auto">
            R$ ${ convertFloatToMoeda(item.adicional.valor) }
            </span>
            </label>
            `;
        });

        if(produto.categoria && produto.categoria.tipo_pizza == 1){

            // console.log("Buscar sabores e tamanhos ...")
            $('.total-adicional-card').hide()
            $('#modalEscolherAdicionais .valor_unitario').hide()
            $('.loader').css('display', 'flex')
            $('.loader-text').text('Buscando sabores...')
            $.get(path_url + "api/pdv-mobo/sabores-tamanhos",
            {
                empresa_id: $('#empresa_id').val(),
                produto_id: produto.id,
                sabores_escolhidos: JSON.stringify(produto.sabores_escolhidos)
            })
            .done((data) => {
                // console.clear()
                // console.log("sabores_escolhidos", produto.sabores_escolhidos)
                saboresEscolhidos = produto.sabores_escolhidos
                calcularValorPizza()
                $('#modalEscolherAdicionais .sabores').html(data)

                setTimeout(() => {
                    if(produto.tamanho_id){
                        $('#tamanhoPizza').val(produto.tamanho_id).change()
                    }
                }, 100)
            })
            .fail((e) => {
                console.log(e);
                toastr.error("Não foi possivel buscar tamanhos e sabores!")
            })
            .always(() => {
                $('.loader').css('display', 'none')
            });
        }

        $('#modalEscolherAdicionais .adicionais').html(html);
        atualizarTotalAdicionais()
    }, 300);
});

function atualizarTotalAdicionais() {
    let total = convertMoedaToFloat($('#modalEscolherAdicionais .valor_unitario').text());

    $('.chk-adicional:checked').each(function () {
        total += parseFloat($(this).data('valor'));
    });

    $('#totalAdicionais').text(convertFloatToMoeda(total));
    calcularValorPizza()
}

$(document).on('click', '#btnConfirmarAdicionais', function () {
    adicionaisEscolhidos = []
    adicionaisEscolhidosStr = []
    $('.chk-adicional:checked').each(function () {
        adicionaisEscolhidos.push(parseInt($(this).val()))
        adicionaisEscolhidosStr.push($(this).next().text())
    });

    if ($("#tamanhoPizza").length && saboresEscolhidos.length == 0) {
        toastr.warning("Selecione o tamanho!");
        return;
    }

    $('#modalEscolherAdicionais').modal('hide');
    setTimeout(() => {
        $('#modalProduto').modal('show');
        let valorComAdicional = 0
        if ($("#tamanhoPizza").length > 0) {
            valorComAdicional = convertMoedaToFloat($('#valorPizza').text())
            $('#modalValor').val(convertFloatToMoeda(valorComAdicional))
            // console.log("fechando", saboresEscolhidos)
            produto.sabores_escolhidos = saboresEscolhidos
            produto.sabores_escolhidos_str = saboresEscolhidosStr
            produto.tamanho_id = $('#tamanhoPizza').val()

        }else{
            valorComAdicional = convertMoedaToFloat($('#totalAdicionais').text())
            $('#modalValor').val(convertFloatToMoeda(valorComAdicional))
            produto.tamanho_id = null
        }

        produto.valor_com_adicional = valorComAdicional
        produto.adicionais_escolhidos = adicionaisEscolhidos
        produto.adicionais_escolhidos_str = adicionaisEscolhidosStr
    }, 300);
});

$(document).on('click', '#btnSalvarComanda', function () {
    let pedido_id = $('#pedido_id').val()
    if(!pedido_id){
        toastr.error("Não é possível salvar comanda sem referência!");
        return;
    }

    let total = $('#btnTotal').text();
    let data = {
        pedido_id: pedido_id,
        itens: carrinho,
        total: convertMoedaToFloat(total),
        cliente_id: clienteSelecinado
    }

    // console.log("comanda", data)

    $.post(path_url + "api/pdv-mobo/update-comanda", data)
    .done((success) => {
        // console.log(success)
    }).fail((err) => {
        console.log(err)
    });
});

$(document).on("keyup", "#filtrarSabores", function () {
    let filtro = $(this).val().toLowerCase().trim();

    $(".sabor-item").each(function () {
        let texto = $(this).text().toLowerCase();

        if (texto.includes(filtro)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

$(document).on('click', '#btnCancelarAdicionais', function () {
    adicionaisEscolhidos = []
    adicionaisEscolhidosStr = []
    $('#modalEscolherAdicionais').modal('hide');
    setTimeout(() => {
        $('#modalProduto').modal('show');
    }, 200)

});

var maximoSabores = null
$(document).on('change', '#tamanhoPizza', function () {
    tamanho_id = $(this).val()
    maximoSabores = $('#tamanhoPizza option:selected').data('max_sabores');

    if(saboresEscolhidos.length == 0){
        saboresEscolhidos.push(produto.id)
        saboresEscolhidosStr.push(produto.nome);
    }
    calcularValorPizza();

    if(tamanho_id){
        $('.containerSabores').removeClass('d-none')
    }else{
        $('.containerSabores').addClass('d-none')
    }
})

$(document).on('change', '.chk-adicional', function () {
    atualizarTotalAdicionais();
});

$(document).on("keyup", "#filtrarComandas", function () {
    let filtro = $(this).val().toLowerCase().trim();

    $(".grid-comandas a").each(function () {
        let texto = $(this).text().toLowerCase();

        if (texto.includes(filtro)) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
});

$(document).on("click", ".sabor-item", function () {

    let id = $(this).data("id");

    if (saboresEscolhidos.includes(id)) {
        saboresEscolhidos = saboresEscolhidos.filter(s => s !== id);
        $(this).removeClass("selecionado");
    } else {

        if(saboresEscolhidos.length+1 <= maximoSabores){
            saboresEscolhidos.push(id);
            saboresEscolhidosStr.push($(this).find('.sabor-nome').text());
            $(this).addClass("selecionado");
        }else{
            toastr.warning(`Escolha até ${maximoSabores} sabor(es)!`)
            return
        }
    }

    calcularValorPizza();
});

function calcularValorPizza() {
    if (!tamanho_id) return;

    if (saboresEscolhidos.length === 0) {
        return;
    }

    let valoresSabores = [];

    let somaSabores = 0
    let maiorValor = 0

    let tipo_divisao_pizza = $('#tipo_divisao_pizza').val()

    saboresEscolhidos.forEach(id => {
        let elemento = $('.sabores-wrapper').find('.sabor_'+id)
        // console.log(elemento)
        // console.log(elemento.data('valores'))
        valores = elemento.data('valores')
        // let sabor = saboresPizza.find(s => s.id == id);

        let t = valores.find(v => v.tamanho_id == tamanho_id);
        let valor = parseFloat(t.valor)

        if(valor > maiorValor){
            maiorValor = valor
        }

        somaSabores += valor
        
    });

    let somaAdicionais = 0
    $('.chk-adicional:checked').each(function () {
        somaAdicionais += parseFloat($(this).data('valor'));
    });

    let valorFinal = maiorValor;
    if(tipo_divisao_pizza == 'divide'){
        valorFinal = somaSabores/saboresEscolhidos.length
    }

    $('#valorPizza').text(convertFloatToMoeda(valorFinal+somaAdicionais));
}

function abrirPopupImpressao(url) {
    window.open(
        url,
        "popupCupom",
        "width=400,height=600,scrollbars=yes,resizable=yes"
        );
}



