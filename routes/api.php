<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/cidadePorNome/{nome}', 'HelperController@cidadePorNome');
Route::get('/cidadePorCodigoIbge/{codigo}', 'HelperController@cidadePorCodigoIbge');
Route::get('/cidadePorId/{id}', 'HelperController@cidadePorId');
Route::get('/buscaCidades', 'HelperController@buscaCidades');
Route::get('/planos-conta', 'HelperController@planoContas');
Route::get('/contas-empresa', 'HelperController@contasEmpresa');
Route::get('/conta-boleto', 'HelperController@contaBoleto');
Route::get('/contas-empresa-count', 'HelperController@contasEmpresaCount');
Route::get('/video-suporte', 'HelperController@videoSuporte');
Route::get('/etiqueta', 'HelperController@etiqueta');
Route::post('/wc-store', 'WoocommercePedidoController@storePedido');
Route::post('/wc-update', 'WoocommercePedidoController@updatePedido');

Route::middleware(['valid'])->group(function () {
    Route::group(['prefix' => 'nfe'], function () {
        Route::post('/emitir', 'NFeController@emitir');
        Route::post('/xml-temporario', 'NFeController@xmlTemporario');
        Route::post('/danfe-temporario', 'NFeController@danfeTemporario');
        Route::post('/consultar', 'NFeController@consultar');
        Route::post('/corrigir', 'NFeController@corrigir');
        Route::post('/cancelar', 'NFeController@cancelar');
        Route::post('/inutilizar', 'NFeController@inutilizar');
        Route::post('/gerarNfe', 'NFeController@gerarNfe');
    });
});

Route::middleware(['validNfce'])->group(function () {
    Route::group(['prefix' => 'nfce'], function () {
        Route::post('/emitir', 'NFCeController@emitir');
        Route::post('/xml-temporario', 'NFCeController@xmlTemporario');
        Route::post('/cancelar', 'NFCeController@cancelar');
        Route::post('/consultar', 'NFCeController@consultar');
        Route::post('/inutilizar', 'NFCeController@inutilizar');
        // Route::post('/gerarNfce', 'NFCeController@gerarNfce');
    });
});

Route::post('/nfce/gerarVenda', 'NFCeController@gerarVenda');
Route::post('/nfce/gerarNfce', 'NFCeController@gerarNfce');


Route::middleware(['validaCTe'])->group(function () {
    Route::group(['prefix' => 'cte'], function () {
        Route::post('/emitir', 'CTeController@emitir');
        Route::post('/xml-temporario', 'CTeController@xmlTemporario');
        Route::post('/dacte-temporario', 'CTeController@dacteTemporario');

        Route::post('/cancelar', 'CTeController@cancelar');
        Route::post('/consultar', 'CTeController@consultar');
    });
});

//grupo para emissão painel
Route::group(['prefix' => 'nfe_painel'], function () {
    Route::post('/emitir', 'NFePainelController@emitir')->middleware('validaNFe');
    Route::post('/cancelar', 'NFePainelController@cancelar');
    Route::post('/corrigir', 'NFePainelController@corrigir');
    Route::post('/consultar', 'NFePainelController@consultar');
    Route::post('/inutilizar', 'NFePainelController@inutilizar');
    Route::post('/consulta-status-sefaz', 'NFePainelController@consultaStatusSefaz');
    Route::get('/find', 'NFePainelController@find');
    Route::post('/send-mail', 'NFePainelController@sendMail');
});

Route::group(['prefix' => 'nfce_painel'], function () {
    Route::post('/emitir', 'NFCePainelController@emitir')->middleware('validaNFCe');
    Route::post('/cancelar', 'NFCePainelController@cancelar');
    Route::post('/consultar', 'NFCePainelController@consultar');
    Route::post('/consulta-status-sefaz', 'NFCePainelController@consultaStatusSefaz');
    Route::post('/transmitir-contigencia', 'NFCePainelController@transmitirContigencia');

    Route::post('/send-mail', 'NFCePainelController@sendMail');
});

Route::group(['prefix' => 'cte_painel'], function () {
    Route::post('/emitir', 'CTePainelController@emitir')->middleware('validaCTe');
    Route::post('/cancelar', 'CTePainelController@cancelar');
    Route::post('/corrigir', 'CTePainelController@corrigir');
    Route::post('/consultar', 'CTePainelController@consultar');
});

Route::group(['prefix' => 'cte_os_painel'], function () {
    Route::post('/emitir', 'CTeOsPainelController@emitir');
    Route::post('/cancelar', 'CTeOsPainelController@cancelar');
    Route::post('/corrigir', 'CTeOsPainelController@corrigir');
    Route::post('/consultar', 'CTeOsPainelController@consultar');
});

Route::group(['prefix' => 'mdfe_painel'], function () {
    Route::post('/emitir', 'MDFePainelController@emitir')->middleware('validaMDFe');
    Route::post('/cancelar', 'MDFePainelController@cancelar');
    Route::post('/corrigir', 'MDFePainelController@corrigir');
    Route::post('/consultar', 'MDFePainelController@consultar');
});

Route::group(['prefix' => 'mdfe'], function () {
    Route::get('/linhaInfoDescarregamento', 'MdfeController@linhaInfoDescarregamento');
    Route::get('/vendas-aprovadas', 'MdfeController@vendasAprovadas');
    Route::post('/cancelar', 'MdfeController@cancelar');
});

Route::group(['prefix' => 'graficos'], function () {
    Route::get('/grafico-vendas-mes', 'GraficoController@graficoVendasMes');
    Route::get('/grafico-compras-mes', 'GraficoController@graficoComprasMes');
    Route::get('/grafico-mes', 'GraficoController@graficoMes');
    Route::get('/grafico-mes-contador', 'GraficoController@graficoMesContador');
    Route::get('/grafico-ult-meses', 'GraficoController@graficoUltMeses');
    Route::get('/grafico-conta-receber', 'GraficoController@graficoContaReceber');
    Route::get('/grafico-conta-pagar', 'GraficoController@graficoContaPagar');
    Route::get('/grafico-mes-cte', 'GraficoController@graficoMesCte');
    Route::get('/grafico-mes-mdfe', 'GraficoController@graficoMesMdfe');
    Route::get('/dados-cards', 'GraficoController@dadosDards');
});

Route::group(['prefix' => 'cardapio'], function () {
    Route::get('/switch-categoria', 'ProdutoCardapioController@switchCategoria');
});

Route::group(['prefix' => 'servico-marketplace'], function () {
    Route::get('/switch-categoria', 'MarketPlaceController@switchCategoria');
});

Route::group(['prefix' => 'produtos-delivery'], function () {
    Route::get('/switch-categoria', 'ProdutoDeliveryController@switchCategoria');
});

Route::group(['prefix' => 'produtos-ecommerce'], function () {
    Route::get('/switch-categoria', 'ProdutoEcommerceController@switchCategoria');
});

Route::get('/paymentStatus/{id}', 'PaymentController@status');
Route::get('/payment-status-asaas', 'PaymentController@statusAsaas');
Route::post('/asaas-confirmacao', 'FinanceiroBoletoController@confirmacao');

Route::group(['prefix' => 'empresas'], function () {
    Route::get('/', 'EmpresaController@pesquisa');
    Route::get('/find-all', 'EmpresaController@findAll');
    Route::get('/find-boleto', 'EmpresaController@findBoleto');
    Route::get('/find-user', 'EmpresaController@findUser');
    Route::get('/find', 'EmpresaController@find');
});

Route::get('/servicos-reserva', 'ServicoController@pesquisaReserva');
Route::group(['prefix' => 'servicos'], function () {
    Route::get('/', 'ServicoController@pesquisa');
    Route::get('/find/{id}', 'ServicoController@find');
});

Route::group(['prefix' => 'veiculos'], function () {
    Route::get('/', 'VeiculoController@pesquisa');
});

Route::group(['prefix' => 'variacoes'], function () {
    Route::get('/modelo', 'VariacaoController@modelo');
    Route::get('/modelo-subvariacoes', 'VariacaoController@modeloVariacoes');
    Route::get('/find', 'VariacaoController@find');
    Route::get('/findById', 'VariacaoController@findById');
});

Route::group(['prefix' => 'combos'], function () {
    Route::get('/modelo', 'ComboController@modelo');
});

Route::group(['prefix' => 'crm'], function () {
    Route::get('/referenciar-registro', 'CrmController@referenciaRegistro');
    Route::get('/modal', 'CrmController@modal');
});

Route::group(['prefix' => 'localizacao'], function () {
    Route::get('/find-number-doc', 'LocalizacaoController@findNumberDoc');
});

Route::group(['prefix' => 'planos'], function () {
    Route::get('/find', 'PlanoController@find');
    Route::get('/findOne/{id}', 'PlanoController@findOne');
});

Route::group(['prefix' => 'vendas'], function () {
    Route::get('/pesquisa', 'VendaController@pesquisa');
});

Route::group(['prefix' => 'orcamentos'], function () {
    Route::get('/valida-desconto', 'OrcamentoController@validaDesconto');
});

Route::group(['prefix' => 'metas'], function () {
    Route::get('/vendas-funcionario', 'MetaController@vendasFuncionario');
    Route::get('/vendas-funcionario-grafico', 'MetaController@vendasFuncionarioGrafico'); 
    Route::get('/os-funcionario', 'MetaController@ordemServicoFuncionario');
    Route::get('/os-funcionario-grafico', 'MetaController@ordemServicoFuncionarioGrafico');

    Route::get('/vendas-periodo', 'MetaController@vendasPeriodo');
});

Route::get('/produtos-composto', 'ProdutoController@pesquisaCompostos');
Route::get('/produtos-combo', 'ProdutoController@pesquisaCombo');
Route::get('/produtos-filtro', 'ProdutoController@pesquisaFiltro');
Route::get('/produtos-reserva', 'ProdutoController@pesquisaReserva');
Route::group(['prefix' => 'produtos'], function () {
    Route::get('/', 'ProdutoController@pesquisa');
    Route::get('/com-estoque', 'ProdutoController@pesquisaComEstoque');
    Route::get('/codigo-unico', 'ProdutoController@codigoUnico');
    Route::get('/cardapio', 'ProdutoController@pesquisaCardapio');
    Route::get('/delivery', 'ProdutoController@pesquisaDelivery');
    Route::get('/find', 'ProdutoController@find');
    Route::get('/findId/{id}', 'ProdutoController@findId');
    Route::get('/findWithLista', 'ProdutoController@findWithLista');
    Route::get('/padrao', 'ProdutoController@padrao');
    Route::get('/findByCategory', 'ProdutoController@findByCategory');
    Route::get('/all', 'ProdutoController@all');

    Route::get('/get-pizzas', 'ProdutoController@getPizzas');
    Route::get('/calculo-pizza', 'ProdutoController@calculoPizza');

    Route::get('/findByBarcode', 'ProdutoController@findByBarcode');
    Route::get('/findByBarcodeReference', 'ProdutoController@findByBarcodeReference');
    Route::get('/findByBarcodeReference2', 'ProdutoController@findByBarcodeReference2');
    Route::get('/info-vencimento/{id}', 'ProdutoController@infoVencimento');
    Route::get('/valida-estoque', 'ProdutoController@validaEstoque');
    Route::post('/marca-store', 'ProdutoController@marcaStore');
    Route::post('/categoria-store', 'ProdutoController@categoriaStore');
    Route::get('/valida-atacado', 'ProdutoController@validaAtacado');
    Route::get('/dados-produto-unico/{id}', 'ProdutoController@dadosProdutoUnico');
    Route::get('/info', 'ProdutoController@info');
    Route::get('/linha-dimensao', 'ProdutoController@linhaDimensao');
    Route::get('/get-dimensao-edit', 'ProdutoController@getDimensaoEdit');
    Route::post('/alterar-gerencia-estoque', 'ProdutoController@alterarGerenciamentoEstoque');
    
});

Route::group(['prefix' => 'nfse'], function () {
    Route::post('/transmitir', 'NotaServicoController@transmitir');
    Route::post('/consultar', 'NotaServicoController@consultar');
    Route::post('/cancelar', 'NotaServicoController@cancelar');
});

Route::group(['prefix' => 'ncm'], function () {
    Route::get('/', 'NcmController@pesquisa');
    Route::get('/valida', 'NcmController@valida');
    Route::get('/carregar', 'NcmController@carregar');
});

Route::group(['prefix' => 'usuarios'], function () {
    Route::post('/set-sidebar', 'UserController@setSidebar');
    Route::get('/', 'UserController@usuarios');
});

Route::group(['prefix' => 'clientes'], function () {
    Route::get('/find/{id}', 'ClienteController@find');
    Route::get('/cashback/{id}', 'ClienteController@cashback');
    Route::get('/pesquisa', 'ClienteController@pesquisa');
    Route::get('/pesquisa-delivery', 'ClienteController@pesquisaDelivery');
    Route::post('/store', 'ClienteController@store');
    Route::get('/consulta-debito', 'ClienteController@consultaDebitos');
});

Route::group(['prefix' => 'motoboys'], function () {
    Route::get('/calc-comissao', 'MotoboyController@calcComissao');
});

Route::group(['prefix' => 'fornecedores'], function () {
    Route::get('/find/{id}', 'FornecedorController@find');
    Route::get('/pesquisa', 'FornecedorController@pesquisa');
    Route::post('/store', 'FornecedorController@store');
    
});

Route::group(['prefix' => 'funcionarios'], function () {
    Route::get('/pesquisa', 'FuncionarioController@pesquisa');
    Route::get('/find', 'FuncionarioController@find');
    Route::get('/valida-atendimento', 'FuncionarioController@validaAtendimento');
});

Route::group(['prefix' => 'lista-preco'], function () {
    Route::get('/pesquisa', 'ListaPrecoController@pesquisa');
    Route::get('/find', 'ListaPrecoController@find');
});

Route::group(['prefix' => 'transportadoras'], function () {
    Route::get('/find/{id}', 'TransportadoraController@find');
});

Route::group(['prefix' => 'interrupcao'], function () {
    Route::post('/store-motivo', 'InterrupcaoController@storeMotivo');
});

Route::group(['prefix' => 'conta-receber'], function () {
    Route::get('/recorrencia', 'ContaReceberController@recorrencia');
});

Route::group(['prefix' => 'conta-pagar'], function () {
    Route::get('/recorrencia', 'ContaPagarController@recorrencia');
});

Route::group(['prefix' => 'ecommerce'], function () {
    Route::get('/calcular-frete', 'EcommerceController@calcularFrete');
    Route::get('/valida-email', 'EcommerceController@validaEmail');
    Route::get('/consulta-pix', 'EcommerceController@consultaPix');
    Route::get('/variacao', 'EcommerceController@variacao');
});

Route::group(['prefix' => 'frenteCaixa'], function () {
    Route::get('/linhaProdutoVenda', 'FrontBoxController@linhaProdutoVenda');
    Route::get('/linhaProdutoVendaAdd', 'FrontBoxController@linhaProdutoVendaAdd');
    Route::get('/linhaParcelaVenda', 'FrontBoxController@linhaParcelaVenda');
    Route::post('/store', 'FrontBoxController@store');
    Route::post('/suspender', 'FrontBoxController@suspender');
    Route::put('/update/{id}', 'FrontBoxController@update');
    Route::get('/buscaFuncionario/{id}', 'FrontBoxController@buscaFuncionario');
    Route::get('/venda-suspensas', 'FrontBoxController@vendasSuspensas');
    Route::get('/gerar-fatura', 'FrontBoxController@gerarFatura');
    Route::get('/gerar-fatura-pdv', 'FrontBoxController@gerarFaturaPdv');
    Route::get('/gerar-fatura-pdv2', 'FrontBoxController@gerarFaturaPdv2');

    Route::get('/categorias-page', 'FrontBoxController@categoriasPage');
    Route::get('/marcas-page', 'FrontBoxController@marcasPage');
    Route::get('/produtos-page', 'FrontBoxController@produtosPage');
    Route::post('/add-produto', 'FrontBoxController@addProduto');
    Route::get('/pesquisa-produto', 'FrontBoxController@pesquisaProduto');
    Route::get('/edit-item', 'FrontBoxController@editItem');
    Route::post('/qr-code-pix', 'FrontBoxController@qrCodePix');
    Route::get('/consulta-pix', 'FrontBoxController@consultaPix');

});

Route::group(['prefix' => 'financeiro-boleto'], function () {
    Route::post('/store', 'FinanceiroBoletoController@store');
    Route::get('/modal', 'FinanceiroBoletoController@modal');
});

Route::group(['prefix' => 'tef'], function () {
    Route::get('/verifica-ativo', 'TefController@verificaAtivo');
    Route::post('/store', 'TefController@store');
    Route::post('/consulta', 'TefController@consulta');
    Route::post('/cancelar', 'TefController@cancelar');
    Route::post('/consulta-cancelamento', 'TefController@consultaCancelamento');
    Route::post('/imprimir', 'TefController@imprimir');
});

Route::group(['prefix' => 'trocas'], function () {
    Route::post('/store', 'TrocaController@store');
});

Route::group(['prefix' => 'manifesto'], function () {
    Route::post('/novos-documentos', 'ManifestoController@novosDocumentos');
});

Route::post('/mercado-livre-notification', 'MercadoLivreController@notification');

Route::group(['prefix' => 'mercadolivre'], function () {
    Route::get('/get-categorias', 'MercadoLivreController@getCategorias');
    Route::get('/get-tipo-publicacao', 'MercadoLivreController@getTiposPublicacao');
});

Route::group(['prefix' => 'nuvemshop'], function () {
    Route::get('/get-categorias', 'NuvemShopController@getCategorias');
});

Route::group(['prefix' => 'categorias-produto-subcategoria'], function () {
    Route::get('/', 'CategoriaProdutoController@categoriaParaSubcategoria');
});

Route::group(['prefix' => 'subcategorias'], function () {
    Route::get('/', 'CategoriaProdutoController@subcategorias');
});

Route::group(['prefix' => 'reservas'], function () {
    Route::get('/disponiveis', 'ReservaController@disponiveis');
    Route::get('/dados-acomodacao', 'ReservaController@dadosAcomodacao');
    Route::get('/dados-hospedes', 'ReservaController@dadosHospedes');
});

Route::get('/notificacoes-pedido', 'NotificacaoController@index');
Route::get('/notificacoes-delivery', 'NotificacaoController@delivery');
Route::get('/notificacoes-ecommerce', 'NotificacaoController@ecommerce');
Route::post('/notificacoes-set-status', 'NotificacaoController@setStatus');
Route::get('/notificacoes-alertas', 'NotificacaoController@alertas');
Route::get('/notificacoes-alertas-super', 'NotificacaoController@alertaSuper');

Route::group(['prefix' => 'ordemServico'], function () {
    Route::get('/linhaServico', 'OrdemServicoController@linhaServico');
    Route::get('/linhaProduto', 'OrdemServicoController@linhaProduto');
    Route::get('/find/{id}', 'OrdemServicoController@find');
    Route::get('/findProduto/{id}', 'OrdemServicoController@findProduto');
    Route::get('/findFuncionario/{id}', 'OrdemServicoController@findFuncionario');
    Route::get('/linhaFuncionario', 'OrdemServicoController@linhaFuncionario');
    Route::get('/medicos', 'OrdemServicoController@medicos');
    Route::get('/convenios', 'OrdemServicoController@convenios');
    Route::get('/laboratorios', 'OrdemServicoController@laboratorios');
    Route::get('/tipos-armacao', 'OrdemServicoController@tiposArmacao');
});

Route::group(['prefix' => 'agendamentos'], function () {
    Route::get('/buscar-horarios', 'AgendamentoController@buscarHorarios');
    Route::post('/verificaDia', 'AgendamentoController@verificaDia');
});

Route::group(['prefix' => 'funcionamentos'], function () {
    Route::get('/diasDoFuncionario', 'FuncionamentoController@diasDoFuncionario');
});

Route::group(['prefix' => 'pedidos'], function () {
    Route::get('/itens-pendentes', 'PedidoController@itensPendentes');
});

Route::post('/cardapio-set-config', 'CardapioController@setConfig');
Route::get('/get-tipos-pagamento', 'CardapioController@tiposDePagamento');

Route::middleware(['authCardapio'])->group(function () {
    Route::group(['prefix' => 'app-cardapio'], function () {
        Route::get('/get-categorias', 'CardapioController@categorias');
        Route::get('/get-categoria/{id}', 'CardapioController@categoria');
        Route::get('/get-produto/{id}', 'CardapioController@produto');
        Route::post('/get-ingredientes', 'CardapioController@ingredientes');
        Route::get('/get-produtos-pesquisa', 'CardapioController@pesquisa');
        Route::get('/get-destaques', 'CardapioController@destaques');
        Route::get('/get-config', 'CardapioController@config');
        Route::post('/store-pedido', 'CardapioController@storePedido');
        Route::post('/store-mesa', 'CardapioController@storeMesa');
        Route::get('/get-conta', 'CardapioController@conta');
        Route::post('/call-garcom', 'CardapioController@chamarGarcom');
        Route::post('/finalizar-conta', 'CardapioController@finalizarConta');
        Route::get('/pedido-emAtendimento', 'CardapioController@emAtendimento');
        Route::get('/tamanhos-pizza', 'CardapioController@tamanhosPizza');
    });
});

Route::post('/comanda-set-config', 'Comanda\\ConfigController@setConfig');

Route::middleware(['authCardapio'])->group(function () {
    // app garçom
    Route::group(['prefix' => 'app-garcom'], function () {
        Route::get('/get-comandas', 'Comanda\\ComandaController@comandas');
        Route::get('/find-comanda', 'Comanda\\ComandaController@find');
        Route::get('/find-produto', 'Comanda\\ComandaController@produto');
        Route::get('/get-produtos', 'Comanda\\ComandaController@produtos');
        Route::post('/store-item', 'Comanda\\ComandaController@storeItem');
        Route::post('/remove-item', 'Comanda\\ComandaController@removeItem');
        Route::get('/find-cliente', 'Comanda\\ClienteController@findCliente');
        Route::post('/open-comanda', 'Comanda\\ComandaController@openComanda');
        Route::post('/fechar-comanda', 'Comanda\\ComandaController@fecharComanda');
        Route::get('/get-tamanhos', 'Comanda\\ComandaController@getTamanhos');
        Route::post('/valor-pizza', 'Comanda\\ComandaController@valorPizza');
        Route::get('/get-sabores', 'Comanda\\ComandaController@getSabores');

        Route::get('/clientes', 'Comanda\\ClienteController@index');
        Route::post('/clientes-store', 'Comanda\\ClienteController@store');
        Route::get('/cidades', 'Comanda\\ClienteController@cidades');

        Route::get('/orcamentos', 'Comanda\\OrcamentoController@index');
        Route::get('/orcamento', 'Comanda\\OrcamentoController@find');
        Route::get('/orcamentos-produtos', 'Comanda\\OrcamentoController@produtos');
        Route::get('/tipos-pagamento', 'Comanda\\OrcamentoController@tiposDePagamento');
        Route::post('/store-orcamento', 'Comanda\\OrcamentoController@store');
        Route::delete('/orcamentos-destroy', 'Comanda\\OrcamentoController@destroy');
        Route::put('/update-orcamento/{id}', 'Comanda\\OrcamentoController@update');

        Route::get('/pre-vendas', 'Comanda\\PreVendaController@index');
        Route::post('/store-pre-venda', 'Comanda\\PreVendaController@store');
        Route::put('/update-pre-venda/{id}', 'Comanda\\PreVendaController@update');
        Route::delete('/pre-venda-destroy', 'Comanda\\PreVendaController@destroy');

        Route::get('/listas-preco', 'Comanda\\OrcamentoController@listasDePreco');
        Route::get('/dados-funcionario', 'Comanda\\OrcamentoController@dadosFuncionario');
    });
});

Route::group(['prefix' => 'pre-venda'], function () {
    Route::get('/finalizar/{id}', 'PreVendaController@finalizar');
});

Route::group(['prefix' => 'delivery-link'], function () {
    Route::get('/cupom', 'Delivery\\HelperController@cupom');
    Route::get('/valida-fone', 'Delivery\\HelperController@validaFone');
    Route::post('/cliente-store', 'Delivery\\HelperController@clienteStore');
    Route::get('/set-endereco', 'Delivery\\HelperController@setEndereco');
    Route::get('/hash-pizzas', 'Delivery\\HelperController@hashPizzas');
    Route::get('/valor-pizza', 'Delivery\\HelperController@valorPizza');

    Route::post('/store-order-pix', 'Delivery\\HelperController@storePix');
    Route::get('/consulta-pix', 'Delivery\\HelperController@consultaPix');
    Route::get('/consulta-pedido', 'Delivery\\HelperController@consultaPedido');


    //novo
    Route::get('/produto-modal/{hash}', 'Delivery\\ProdutoController@produtoModal')->name('produto-delivery.modal');
    Route::post('/remove-item', 'Delivery\\CarrinhoController@removeItem');
    Route::post('/carrinho-count', 'Delivery\\CarrinhoController@carrinhoCount');
    Route::post('/atualiza-quantidade', 'Delivery\\CarrinhoController@atualizaQuantidade');
    Route::post('/valida-estoque', 'Delivery\\CarrinhoController@validaEstoque');
    Route::get('/carrinho-modal', 'Delivery\\CarrinhoController@carrinhoModal')->name('carrinho.modal');
    Route::post('/atualiza-carrinho', 'Delivery\\CarrinhoController@atualizaCarrinho');
    Route::post('/comprovante-carrinho', 'Delivery\\CarrinhoController@comprovanteCarrinho');
    Route::get('/find-endereco', 'Delivery\\CarrinhoController@findEndereco');
    Route::get('/pesquisa-pizza', 'Delivery\\ProdutoController@pesquisaPizza');
    Route::get('/monta-pizza', 'Delivery\\ProdutoController@montaPizza');
    Route::get('/get-enderecos', 'Delivery\\CarrinhoController@getEnderecos');

    Route::get('/servico-modal/{hash}', 'Delivery\\ServicoController@servicoModal')->name('servico-delivery.modal');
    Route::get('/get-atendentes', 'Delivery\\ServicoController@getAtendentes');


});

//rotas de delivery
Route::middleware(['authDelivery'])->group(function () {
    Route::group(['prefix' => 'delivery'], function(){
        Route::get('/categorias', 'Delivery\\ProdutoController@all');
        Route::get('/produto/{id}', 'Delivery\\ProdutoController@find');
        Route::get('/config', 'Delivery\\ConfigController@index');
        Route::get('/cupom', 'Delivery\\ConfigController@cupom');

        Route::post('/endereco-save', 'Delivery\\ClienteController@enderecoSave');
        Route::post('/endereco-update', 'Delivery\\ClienteController@enderecoUpdate');
        Route::post('/update-endereco-padrao', 'Delivery\\ClienteController@updateEnderecoPadrao');

        Route::post('/login', 'Delivery\\ClienteController@login');
        Route::post('/send-code', 'Delivery\\ClienteController@sendCode');
        Route::post('/refresh-code', 'Delivery\\ClienteController@refreshCode');
        Route::post('/cliente-save', 'Delivery\\ClienteController@clienteSave');
        Route::post('/cliente-update', 'Delivery\\ClienteController@clienteUpdate');
        Route::post('/cliente-update-senha', 'Delivery\\ClienteController@clienteUpdateSenha');
        Route::get('/find-cliente', 'Delivery\\ClienteController@findCliente');
        Route::post('/pedido-save', 'Delivery\\PedidoController@save');

        Route::get('/adicionais', 'Delivery\\ProdutoController@adicionais');
        Route::get('/carrossel', 'Delivery\\ProdutoController@carrossel');
        Route::get('/bairros', 'Delivery\\ConfigController@bairros');
        Route::post('/gerar-qrcode', 'Delivery\\PedidoController@gerarQrcode');
        Route::post('/status-pix', 'Delivery\\PedidoController@consultaPix');
        Route::post('/ultimo-pedido-confirmar', 'Delivery\\PedidoController@ultimoPedidoParaConfirmar');
        Route::post('/consulta-pedido-lido', 'Delivery\\PedidoController@consultaPedidoLido');

    });
});

Route::post('/nfse-webhook', 'NfseWebHookController@index');

Route::group(['prefix' => 'pdv'], function () {
    Route::post('/login', 'PDV\\LoginController@login');
    Route::post('/produtos', 'PDV\\ProdutoController@produtos');
    Route::post('/categorias', 'PDV\\ProdutoController@categorias');
    Route::post('/clientes', 'PDV\\ClienteController@all');
    Route::post('/vendedores', 'PDV\\ClienteController@vendedores');
    Route::post('/store-venda', 'PDV\\VendaController@store');
    Route::get('/bandeiras-cartao', 'PDV\\VendaController@bandeirasCartao');
    Route::get('/dados-empresa', 'PDV\\LoginController@dadosEmpresa');
    Route::get('/contas-empresa', 'PDV\\VendaController@contasEmpresa');
    Route::get('/tipos-pagamento', 'PDV\\VendaController@tiposPagamento');
    Route::get('/get-caixa', 'PDV\\VendaController@getCaixa');
    Route::get('/get-vendas-caixa', 'PDV\\VendaController@getVendasCaixa');
    Route::post('/store-caixa', 'PDV\\VendaController@storeCaixa');
    Route::post('/store-sangria', 'PDV\\VendaController@storeSangria');
    Route::post('/store-suprimento', 'PDV\\VendaController@storeSuprimento');
    Route::get('/data-home', 'PDV\\VendaController@dataHome');
    Route::get('/lista-preco', 'PDV\\ProdutoController@listaPreco');
    Route::get('/empresa-ativa', 'PDV\\LoginController@empresaAtiva');
    Route::get('/locais-usuario', 'PDV\\VendaController@locaisUsuario');

});

Route::middleware(['validaApiToken'])->group(function () {
    Route::group(['prefix' => 'v1'], function () {
        Route::group(['prefix' => 'categoria-produtos'], function () {
            Route::get('/', 'Token\\CategoriaProdutoController@index');
            Route::get('/{id}', 'Token\\CategoriaProdutoController@find');
            Route::post('/store', 'Token\\CategoriaProdutoController@store');
            Route::put('/update', 'Token\\CategoriaProdutoController@update');
            Route::delete('/delete', 'Token\\CategoriaProdutoController@delete');
        });

        Route::group(['prefix' => 'clientes'], function () {
            Route::get('/', 'Token\\ClienteController@index');
            Route::get('/{id}', 'Token\\ClienteController@find');
            Route::post('/store', 'Token\\ClienteController@store');
            Route::put('/update', 'Token\\ClienteController@update');
            Route::delete('/delete', 'Token\\ClienteController@delete');
        });

        Route::group(['prefix' => 'fornecedores'], function () {
            Route::get('/', 'Token\\FornecedorController@index');
            Route::get('/{id}', 'Token\\FornecedorController@find');
            Route::post('/store', 'Token\\FornecedorController@store');
            Route::put('/update', 'Token\\FornecedorController@update');
            Route::delete('/delete', 'Token\\FornecedorController@delete');
        });

        Route::group(['prefix' => 'produtos'], function () {
            Route::get('/', 'Token\\ProdutoController@index');
            Route::get('/{id}', 'Token\\ProdutoController@find');
            Route::post('/store', 'Token\\ProdutoController@store');
            Route::put('/update', 'Token\\ProdutoController@update');
            Route::delete('/delete', 'Token\\ProdutoController@delete');
        });

        Route::group(['prefix' => 'vendas-pdv'], function () {
            Route::get('/', 'Token\\VendaPdvController@index');
            Route::get('/{id}', 'Token\\VendaPdvController@find');
            Route::post('/store', 'Token\\VendaPdvController@store');
            Route::put('/update', 'Token\\VendaPdvController@update');
            Route::delete('/delete', 'Token\\VendaPdvController@delete');
        });

        Route::group(['prefix' => 'cotacao'], function () {
            Route::get('/', 'Token\\ContacaoController@index');
            Route::get('/{id}', 'Token\\ContacaoController@find');
            Route::post('/store', 'Token\\ContacaoController@store');
            Route::put('/update', 'Token\\ContacaoController@update');
            Route::delete('/delete', 'Token\\ContacaoController@delete');
        });

        Route::group(['prefix' => 'usuarios'], function () {
            Route::get('/', 'Token\\UsuarioController@index');
            Route::get('/{id}', 'Token\\UsuarioController@find');
        });

        Route::group(['prefix' => 'caixa'], function () {
            Route::get('/open', 'Token\\CaixaController@open');
            Route::post('/store', 'Token\\CaixaController@store');
        });

        Route::group(['prefix' => 'natureza-operacao'], function () {
            Route::get('/', 'Token\\NaturezaOperacaoController@index');
            Route::get('/{id}', 'Token\\NaturezaOperacaoController@find');
            Route::post('/store', 'Token\\NaturezaOperacaoController@store');
            Route::put('/update', 'Token\\NaturezaOperacaoController@update');
            Route::delete('/delete', 'Token\\NaturezaOperacaoController@delete');
        });

        Route::group(['prefix' => 'padrao-fiscal'], function () {
            Route::get('/', 'Token\\PadraoFiscalController@index');
            Route::get('/{id}', 'Token\\PadraoFiscalController@find');
            Route::post('/store', 'Token\\PadraoFiscalController@store');
            Route::put('/update', 'Token\\PadraoFiscalController@update');
            Route::delete('/delete', 'Token\\PadraoFiscalController@delete');
        });

    });
});

Route::middleware(['validaApiTokenSuperAdmin'])->group(function () {
    Route::group(['prefix' => 'v1'], function () {
        Route::group(['prefix' => 'planos'], function () {
            Route::get('/', 'TokenSuperAdmin\\PlanoController@index');
            Route::get('/{id}', 'TokenSuperAdmin\\PlanoController@find');
            Route::post('/store', 'TokenSuperAdmin\\PlanoController@store');
            Route::put('/update', 'TokenSuperAdmin\\PlanoController@update');
            Route::delete('/delete', 'TokenSuperAdmin\\PlanoController@delete');
        });

        Route::group(['prefix' => 'empresas'], function () {
            Route::get('/', 'TokenSuperAdmin\\EmpresaController@index');
            Route::get('/{id}', 'TokenSuperAdmin\\EmpresaController@find');
            Route::post('/store', 'TokenSuperAdmin\\EmpresaController@store');
            Route::put('/update', 'TokenSuperAdmin\\EmpresaController@update');
            Route::delete('/delete', 'TokenSuperAdmin\\EmpresaController@delete');
        });

        Route::group(['prefix' => 'usuarios'], function () {
            Route::get('/', 'TokenSuperAdmin\\UsuarioController@index');
            Route::get('/{id}', 'TokenSuperAdmin\\UsuarioController@find');
            Route::post('/store', 'TokenSuperAdmin\\UsuarioController@store');
            Route::put('/update', 'TokenSuperAdmin\\UsuarioController@update');
            Route::delete('/delete', 'TokenSuperAdmin\\UsuarioController@delete');
        });

        Route::group(['prefix' => 'gerenciar-planos'], function () {
            Route::post('/store', 'TokenSuperAdmin\\GerenciarPlanoController@store');
        });
    });
});



