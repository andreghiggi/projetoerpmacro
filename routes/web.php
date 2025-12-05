<?php

use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Row;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();
Route::post('/reset-pass', 'ResetPasswordController@reset')->name('reset.pass');

Route::get('/clear-all', function () {
    \Artisan::call('cache:clear');
    \Artisan::call('config:clear');
    \Artisan::call('view:clear');
    \Artisan::call('route:clear');
    // system('composer dump-autoload');
    return redirect()->back();
})->name('clear');

// Route::get('/', function(){
//     return redirect()->route('home');
// });

// Route::get('/', function(){
//     return redirect()->route('home');
// });

Route::get('/', 'LandingPageController@index');

Route::get('/cotacoes-resposta/{hash_link}', 'CotacaoRespostaController@index')->name('cotacoes.resposta');
Route::get('/ordem-servico-link/{hash}', 'OrdemServicoViewController@index')->name('ordem-servico.link');

Route::post('/cotacoes-resposta-store', 'CotacaoRespostaController@store')->name('cotacoes.resposta-store');
Route::get('/cotacoes-finish', 'CotacaoRespostaController@finish')->name('cotacoes.finish');
Route::get('/imprimir-nfce/{chave}', 'ImprimirNfceController@imprimir');
Route::get('/registro', 'RegistroController@index')->name('registro');

Route::get('/reserva-cliente/{codigo}', 'ReservaClienteController@index');
Route::post('/reserva-checkin-start-cliente/{id}', 'ReservaClienteController@checkinStart')->name('reservas.checkin-start-cliente');

Route::middleware(['validaCardapio'])->group(function () {
    Route::get('cardapio', 'Cardapio\\HomeController@index')->name('cardapio.index');
    Route::get('cardapio-pesquisa', 'Cardapio\\HomeController@pesquisa')->name('cardapio.pesquisa');
    Route::get('cardapio-conta', 'Cardapio\\HomeController@conta')->name('cardapio.conta');
    Route::get('cardapio-produtos-categoria/{hash}', 'Cardapio\\HomeController@produtosDaCategoria')->name('cardapio.produtos-categoria');
    Route::post('cardapio-carrinho-adicionar', 'Cardapio\\CarrinhoController@adicionar')->name('cardapio.adicionar-carrinho');
    Route::get('cardapio-carrinho', 'Cardapio\\CarrinhoController@index')->name('cardapio.carrinho');
    Route::get('cardapio-ofertas', 'Cardapio\\HomeController@ofertas')->name('cardapio.ofertas');
    Route::post('cardapio-enviar-pedido', 'Cardapio\\CarrinhoController@enviarPedido')->name('cardapio.enviar-pedido');
    Route::get('cardapio-produto-detalhe/{hash}', 'Cardapio\\HomeController@produtoDetalhe')->name('cardapio.produto-detalhe');
    Route::get('cardapio-pedir-fechar', 'Cardapio\\CarrinhoController@pedirFechar')->name('cardapio.pedir-fechar');

});

Route::middleware(['validaDelivery'])->group(function () {
    Route::get('food', 'Delivery\\HomeController@index')->name('food.index');
    Route::get('food-produtos-categoria/{hash}', 'Delivery\\HomeController@produtosDaCategoria')->name('food.produtos-categoria');
    Route::get('food-servicos-categoria/{hash}', 'Delivery\\HomeController@servicosDaCategoria')->name('food.servicos-categoria');
    Route::get('produto-food-detalhe/{hash}', 'Delivery\\HomeController@produtoDetalhe')->name('food.produto-detalhe');
    Route::get('servico-detalhe/{hash}', 'Delivery\\HomeController@servicoDetalhe')->name('food.servico-detalhe');
    Route::get('food-pesquisa', 'Delivery\\HomeController@pesquisa')->name('food.pesquisa');
    Route::get('food-ofertas', 'Delivery\\HomeController@ofertas')->name('food.ofertas');
    Route::post('food-carrinho-adicionar', 'Delivery\\CarrinhoController@adicionar')->name('food.adicionar-carrinho');
    
    Route::post('food-carrinho-adicionar-servico', 'Delivery\\CarrinhoController@adicionarServico')->name('food.adicionar-carrinho-servico');
    
    Route::get('food-carrinho', 'Delivery\\CarrinhoController@index')->name('food.carrinho');
    Route::get('food-carrinho-update', 'Delivery\\CarrinhoController@updateQuantidades')->name('food.carrinho-update');
    Route::delete('remove-item-food/{id}', 'Delivery\\CarrinhoController@removeItem')->name('food.remove-item');
    Route::delete('remove-endereco/{id}', 'Delivery\\ClienteController@removeEndereco')->name('food.remove-endereco');
    Route::get('food-pagamento', 'Delivery\\PagamentoController@index')->name('food.pagamento');
    Route::post('food-seta-agendamento', 'Delivery\\PagamentoController@setaAgendamento')->name('food.seta-agendamento');
    Route::get('food-auth', 'Delivery\\ClienteController@auth')->name('food.auth');
    Route::post('food-endereco-store', 'Delivery\\ClienteController@enderecoStore')->name('food.endereco-store');
    Route::post('food-finalizar', 'Delivery\\PagamentoController@finalizar')->name('food.finalizar-pagamento');
    Route::get('pizza-food-detalhe', 'Delivery\\HomeController@pizzaDetalhe')->name('food.pizza-detalhe');
    Route::get('conta', 'Delivery\\ClienteController@conta')->name('food.conta');
    Route::get('pedidos', 'Delivery\\ClienteController@pedidos')->name('food.pedidos');
    Route::post('food-endereco-update', 'Delivery\\ClienteController@enderecoUpdate')->name('food.endereco-update');
    Route::get('food-carrinho-pedir-novamente/{id}', 'Delivery\\PagamentoController@pedirNovamente')->name('food.carrinho-pedir-novamente');
    Route::get('food-aguardando-confirmar', 'Delivery\\PagamentoController@aguardandoConfirmar')->name('food.aguardando-confirmar');
    Route::get('food-logoff', 'Delivery\\ClienteController@logoff')->name('food.logoff');
    Route::get('food-qr_code/{transacao_id}', 'Delivery\\PagamentoController@qrCode')->name('food.qr_code');
    Route::post('food-pagamento-pix', 'Delivery\\PagamentoController@pagamentoPix')->name('food.pagamento-pix');
    Route::post('food-pagamento-cartao', 'Delivery\\PagamentoController@pagamentoCartao')->name('food.pagamento-cartao');
    
});

Route::middleware(['validaEcommerce'])->group(function () {
    Route::get('loja', 'Ecommerce\\HomeController@index')->name('loja.index');
    Route::get('loja-produtos-categoria/{hash}', 'Ecommerce\\HomeController@produtosDaCategoria')->name('loja.produtos-categoria');
    Route::get('produto-ecommerce-detalhe/{hash}', 'Ecommerce\\HomeController@produtoDetalhe')->name('loja.produto-detalhe');
    Route::post('carrinho-adicionar', 'Ecommerce\\CarrinhoController@adicionar')->name('loja.adicionar-carrinho');
    Route::get('carrinho', 'Ecommerce\\CarrinhoController@index')->name('loja.carrinho');
    Route::delete('remove-item-loja/{id}', 'Ecommerce\\CarrinhoController@removeItem')->name('loja.remove-item');
    Route::put('loja-atualiza-quantidade/{id}', 'Ecommerce\\CarrinhoController@atualizaQuantidade')->name('loja.atualiza-quantidade');
    Route::post('carrinho-setar-frete', 'Ecommerce\\CarrinhoController@setarFrete')->name('loja.carrinho-setar-frete');
    Route::get('loja-cadastro', 'Ecommerce\\ClienteController@cadastro')->name('loja.cadastro');
    Route::post('loja-cadastro-store', 'Ecommerce\\ClienteController@cadastroStore')->name('loja.cadastro-store');
    Route::get('loja-pesquisa', 'Ecommerce\\HomeController@pesquisa')->name('loja.pesquisa');
    Route::get('loja-pagamento', 'Ecommerce\\PagamentoController@index')->name('loja.pagamento');

    Route::get('loja-politica-privacidade', 'Ecommerce\\HomeController@politicaPrivacidade')->name('loja.politica-privacidade');
    Route::get('loja-minha-conta', 'Ecommerce\\ClienteController@minhaConta')->name('loja.minha-conta');
    Route::put('loja-update-cliente/{id}', 'Ecommerce\\ClienteController@update')->name('loja.update-cliente');
    Route::post('loja-store-endereco', 'Ecommerce\\ClienteController@storeEndereco')->name('loja.store-endereco');
    Route::get('loja-logoff', 'Ecommerce\\ClienteController@logoff')->name('loja.logoff');
    Route::get('loja-login', 'Ecommerce\\ClienteController@login')->name('loja.login');
    Route::post('loja-auth', 'Ecommerce\\ClienteController@auth')->name('loja.login-auth');
    Route::post('loja-pagamento-pix', 'Ecommerce\\PagamentoController@pagamentoPix')->name('loja.pagamento-pix');
    Route::post('loja-pagamento-novo-pix', 'Ecommerce\\PagamentoController@pagamentoNovoPix')->name('loja.pagamento-novo-pix');
    Route::post('loja-pagamento-boleto', 'Ecommerce\\PagamentoController@pagamentoBoleto')->name('loja.pagamento-boleto');
    Route::post('loja-pagamento-cartao', 'Ecommerce\\PagamentoController@pagamentoCartao')->name('loja.pagamento-cartao');
    Route::post('loja-pagamento-deposito', 'Ecommerce\\PagamentoController@pagamentoDeposito')->name('loja.pagamento-deposito');
    Route::get('loja-finalizar', 'Ecommerce\\PagamentoController@finalizar')->name('loja.finalizar');
    Route::get('loja-finalizar-deposito', 'Ecommerce\\PagamentoController@finalizarDeposito')->name('loja.finalizar-deposito');
    Route::get('loja-nova-chavepix', 'Ecommerce\\PagamentoController@novaChavePix')->name('loja.nova-chavepix');
    Route::post('loja-enviar-comprovante', 'Ecommerce\\PagamentoController@enviarComprovante')->name('loja.enviar-comprovante');

});

Route::middleware(['authh', 'validaEmpresa'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    Route::middleware(['verificaMaster'])->group(function () {
        Route::get('/nfe-all', [App\Http\Controllers\HomeController::class, 'nfe'])->name('nfe-all');
        Route::get('/nfce-all', [App\Http\Controllers\HomeController::class, 'nfce'])->name('nfce-all');
        Route::get('/cte-all', [App\Http\Controllers\HomeController::class, 'cte'])->name('cte-all');
        Route::get('/mdfe-all', [App\Http\Controllers\HomeController::class, 'mdfe'])->name('mdfe-all');
        Route::resource('empresas', 'EmpresaController');
        Route::delete('empresas-destroy-produtos/{id}', 'EmpresaController@destroyProdutos')->name('empresas.destroy-produtos');

        Route::resource('contadores', 'ContadorController');
        Route::get('contadores-financeiro/{id}', 'ContadorController@financeiro')->name('contadores.financeiro');
        Route::get('contadores-financeiro-create/{id}', 'ContadorController@createFinanceiro')->name('contadores.financeiro-create');
        Route::post('contadores-financeiro-store/{id}', 'ContadorController@storeFinanceiro')->name('contadores.financeiro-store');
        Route::put('contadores-add-business/{id}', 'ContadorController@addBusiness')->name('contadores.add-business');
        Route::delete('contadores-destroy-business/{id}', 'ContadorController@destroyBusiness')->name('contadores.destroy-business');
        Route::delete('contadores-destroy-financeiro/{id}', 'ContadorController@destroyFincanceiro')->name('contadores-financeiro.destroy');

        Route::get('escritorio-contabils', 'ContadorController@escritorios')->name('escritorio-contabils');

        Route::resource('planos', 'PlanoController');
        Route::resource('padroes-etiqueta', 'PadraoEtiquetaController');
        Route::resource('segmentos', 'SegmentoController');
        Route::resource('gerenciar-planos', 'GerenciarPlanoController');
        Route::resource('ticket-super', 'TicketSuperController');
        Route::resource('contrato-config', 'ContratoConfigController');
        Route::get('contrato-config-list', 'ContratoConfigController@list')->name('contrato-config.list');

        Route::resource('financeiro-boleto', 'FinanceiroBoletoController');
        Route::get('financeiro-boleto-gerador', 'FinanceiroBoletoController@gerar')->name('financeiro-boleto.gerar');
        Route::get('financeiro-boleto-logs', 'FinanceiroBoletoController@logs')->name('financeiro-boleto.logs');
        Route::get('financeiro-boleto-imprimir/{id}', 'FinanceiroBoletoController@imprimir')->name('financeiro-boleto.print');

        Route::delete('ticket-super-destroy-select', 'TicketSuperController@destroySelecet')->name('ticket-super.destroy-select');

        Route::put('ticket-super-add-mensagem/{id}', 'TicketSuperController@addMensagem')->name('ticket-super.add-mensagem');
        Route::put('ticket-super-update-status/{id}', 'TicketSuperController@updateStatus')->name('ticket-super.update-status');
        Route::delete('ticket-super-destroy-mensagem/{id}', 'TicketSuperController@destroyMensagem')->name('ticket-super.destroy-mensagem');

        Route::resource('planos-pendentes', 'PlanoPendenteController');
        Route::resource('configuracao-super', 'ConfiguracaoSuperController');
        Route::resource('cidades', 'CidadeController');
        Route::resource('bairros-super', 'BairroSuperController');
        Route::resource('video-suporte', 'VideoSuporteController');
        Route::resource('financeiro-plano', 'FinanceiroPlanoController');

        Route::group(['prefix' => '/update-sql'], function () {
            Route::get('/', 'UpdateController@index')->name('update-sql.index');
            Route::get('/update', 'UpdateController@update');
            Route::post('/sql', 'UpdateController@sqlStore');
            Route::post('/run-sql', 'UpdateController@runSql');
        });

        Route::group(['prefix' => '/update-file'], function () {
            Route::get('/', 'UpdateFileController@index')->name('update-file.index');
            Route::post('store', 'UpdateFileController@store')->name('update-file.store');
            Route::get('log', 'UpdateFileController@log')->name('update-file.log');
        });

        Route::get('set-version', 'UpdateFileController@setVersion');

        Route::resource('sugestao', 'SugestaoController');
        Route::post('/sugestao-response/{id}', 'SugestaoController@response')->name('sugestao.response');
        Route::get('/sugestao-like/{id}', 'SugestaoController@like')->name('sugestao.like');
        Route::post('/sugestao-auth', 'SugestaoController@auth')->name('sugestao.auth');

        Route::resource('ncm', 'NcmController');
        Route::get('/ncm-refresh', 'NcmController@refresh')->name('ncm.refresh');
        Route::post('/ncm-store-xls', 'NcmController@import')->name('ncm.store-xls');

        Route::resource('natureza-operacao-super', 'NaturezaOperacaoSuperController');
        Route::resource('padrao-tributacao-produto-super', 'PadraoTributacaoProdutoSuperController');
        Route::resource('logs', 'LogController');
        Route::resource('notificacao-super', 'NotificacaoSuperController');
        Route::delete('notificacao-super-destroy-select', 'NotificacaoSuperController@destroySelecet')->name('notificacao-super.destroy-select');

        Route::resource('ibpt', 'IbptController');
        Route::resource('permissions', 'PermissionController');
        Route::get('/permissions-update-all', 'PermissionController@updateAll')->name('permissions.update-all');

        Route::resource('roles', 'RoleController');
        Route::resource('natureza-operacao-adm', 'NaturezaOperacaoAdmController');
        Route::resource('produtopadrao-tributacao-adm', 'PadraoTributacaoProdutoAdmController');

        Route::group(['prefix' => 'relatorios-adm'], function () {
            Route::get('/', 'RelatorioAdmController@index')->name('relatorios-adm.index');
            Route::get('empresas', 'RelatorioAdmController@empresas')->name('relatorios-adm.empresas');
            Route::get('historico-acesso', 'RelatorioAdmController@historicoAcesso')->name('relatorios-adm.historico-acesso');
            Route::get('planos', 'RelatorioAdmController@planos')->name('relatorios-adm.planos');
            Route::get('certificados', 'RelatorioAdmController@certificados')->name('relatorios-adm.certificados');
            Route::get('resumo-operacional', 'RelatorioAdmController@resumoOperacional')->name('relatorios-adm.resumo-operacional');
        });

        Route::resource('cidades', 'CidadeController');
    });

    // rotas do contador
Route::get('contador-profile', 'ContadorAdminController@profile')->name('contador.profile');
Route::put('contador-profile/{id}', 'ContadorAdminController@profileUpdate')->name('contador.profile-update');
Route::get('contador-set/{id}', 'ContadorAdminController@setEmpresa')->name('contador.set-empresa');
Route::get('contador-empresa', 'ContadorAdminController@show')->name('contador.show');
Route::put('contador-empresa-update/{id}', 'ContadorAdminController@update')->name('contador-empresa.update');
Route::get('contador-empresa-produtos', 'ContadorAdminController@produtos')->name('contador-empresa.produtos');
Route::get('contador-empresa-produtos/{id}', 'ContadorAdminController@produtoShow')->name('contador-empresa-produtos.show');
Route::get('contador-empresa-produtos-edit/{id}', 'ContadorAdminController@produtoEdit')->name('contador-empresa-produtos.edit');
Route::get('contador-empresa-clientes-edit/{id}', 'ContadorAdminController@clienteEdit')->name('contador-empresa-clientes.edit');
Route::get('contador-empresa-clientes', 'ContadorAdminController@clientes')->name('contador-empresa.clientes');
Route::get('contador-empresa-fornecedores', 'ContadorAdminController@fornecedores')->name('contador-empresa.fornecedores');
Route::get('contador-empresa-fornecedores-edit/{id}', 'ContadorAdminController@fornecedorEdit')->name('contador-empresa-fornecedores.edit');

Route::resource('contador-natureza-operacao', 'ContadorNaturezaOperacaoController');
Route::resource('contador-produto-tributacao', 'ContadorPadraoTributacaoProdutoController');

Route::resource('contador-empresas', 'ContadorEmpresaController');
Route::resource('contador-planos', 'ContadorPlanoController');
Route::resource('contador-gerencia', 'ContadorGerenciaPlanoController');

Route::get('contador-produtotributacao-alterar', 'ContadorPadraoTributacaoProdutoController@alterarProdutos')
->name('contador-produto-tributacao.alterar');

Route::post('contador-produtotributacao-set-tributacao', 'ContadorPadraoTributacaoProdutoController@setTributacao')
->name('contador-produto-tributacao.set-tributacao');

Route::put('contador-empresa-produtos-update/{id}', 'ContadorAdminController@produtoUpdate')->name('contador-empresa-produtos.update');
Route::put('contador-empresa-clientes-update/{id}', 'ContadorAdminController@clienteUpdate')->name('contador-empresa-clientes.update');
Route::put('contador-empresa-fornecedores-update/{id}', 'ContadorAdminController@fornecedorUpdate')->name('contador-empresa-fornecedores.update');

Route::get('contador-empresa-create', 'ContadorAdminController@empresaCreate')->name('contador.empresa-create');
Route::post('contador-empresa-store', 'ContadorAdminController@empresaStore')->name('contador.empresa-store');
Route::get('contador-empresa-plano/{empresa_id}', 'ContadorAdminController@plano')->name('contador-empresa.plano');
Route::put('contador-empresa-set-plano/{empresa_id}', 'ContadorAdminController@setPlano')->name('contador-empresa.set-plano');

    //nfe
Route::get('contador-empresa-nfe', 'ContadorAdminNFeController@nfe')->name('contador-empresa.nfe');
Route::get('contador-empresa-nfe-download/{id}', 'ContadorAdminNFeController@downloadNFe')->name('contador-empresa-nfe.download');
Route::get('contador-empresa-nfe-danfe/{id}', 'ContadorAdminNFeController@danfe')->name('contador-empresa-nfe.danfe');
Route::get('contador-empresa-nfe-imprimir-cancela/{id}', 'ContadorAdminNFeController@imprimirCancela')->name('contador-empresa-nfe.imprimir-cancela');
Route::get('contador-empresa-nfe-imprimir-correcao/{id}', 'ContadorAdminNFeController@imprimirCorrecao')->name('contador-empresa-nfe.imprimir-correcao');
Route::get('contador-empresa-nfe-edit/{id}', 'ContadorAdminNFeController@edit')->name('contador-empresa-nfe.edit');
Route::get('contador-empresa-nfe-xml-temp/{id}', 'ContadorAdminNFeController@xmlTemp')->name('contador-empresa-nfe.xml-temp');
Route::get('contador-empresa-nfe-zip', 'ContadorAdminNFeController@downloadZip')->name('contador-empresa-nfe-zip');
Route::put('contador-empresa-nfe-update/{id}', 'ContadorAdminNFeController@nfeUpdate')->name('contador-empresa-nfe.update');

Route::get('contador-empresa-nfe-create', 'ContadorAdminNFeController@create')->name('contador-empresa-nfe.create');
Route::delete('contador-empresa-nfe-destroy/{id}', 'ContadorAdminNFeController@destroy')->name('contador-empresa-nfe.destroy');
Route::post('contador-empresa-nfe-store', 'ContadorAdminNFeController@store')->name('contador-empresa-nfe.store');

    //nfce
Route::get('contador-empresa-nfce', 'ContadorAdminNFCeController@nfce')->name('contador-empresa.nfce');
Route::get('contador-empresa-nfce-download/{id}', 'ContadorAdminNFCeController@downloadNFCe')->name('contador-empresa-nfce.download');
Route::get('contador-empresa-nfce-danfce/{id}', 'ContadorAdminNFCeController@danfce')->name('contador-empresa-nfce.danfce');
Route::get('contador-empresa-nfce-zip', 'ContadorAdminNFCeController@downloadZip')->name('contador-empresa-nfce-zip');
Route::get('contador-empresa-nfce-edit/{id}', 'ContadorAdminNFCeController@edit')->name('contador-empresa-nfce.edit');
Route::get('contador-empresa-nfce-xml-temp/{id}', 'ContadorAdminNFCeController@xmlTemp')->name('contador-empresa-nfce.xml-temp');
Route::put('contador-empresa-nfce-update/{id}', 'ContadorAdminNFCeController@nfceUpdate')->name('contador-empresa-nfce.update');

Route::get('contador-empresa-nfce-create', 'ContadorAdminNFCeController@create')->name('contador-empresa-nfce.create');
Route::delete('contador-empresa-nfce-destroy/{id}', 'ContadorAdminNFCeController@destroy')->name('contador-empresa-nfce.destroy');
Route::post('contador-empresa-nfce-store', 'ContadorAdminNFCeController@store')->name('contador-empresa-nfce.store');

    //cte
Route::get('contador-empresa-cte', 'ContadorAdminCTeController@cte')->name('contador-empresa.cte');
Route::get('contador-empresa-cte-download/{id}', 'ContadorAdminCTeController@downloadCTe')->name('contador-empresa-cte.download');
Route::get('contador-empresa-cte-dacte/{id}', 'ContadorAdminCTeController@dacte')->name('contador-empresa-cte.dacte');
Route::get('contador-empresa-cte-zip', 'ContadorAdminCTeController@downloadZip')->name('contador-empresa-cte-zip');

    //cte
Route::get('contador-empresa-mdfe', 'ContadorAdminMDFeController@mdfe')->name('contador-empresa.mdfe');
Route::get('contador-empresa-mdfe-download/{id}', 'ContadorAdminMDFeController@downloadMDFe')->name('contador-empresa-mdfe.download');
Route::get('contador-empresa-mdfe-dacte/{id}', 'ContadorAdminMDFeController@damdfe')->name('contador-empresa-mdfe.damdfe');
Route::get('contador-empresa-mdfe-zip', 'ContadorAdminMDFeController@downloadZip')->name('contador-empresa-mdfe-zip');
    // rotas do contador fim

Route::get('nfe/imprimir/{id}', 'NfeController@imprimir')->name('nfe.imprimir');
Route::get('nfe-imprimir-carne/{id}', 'NfeController@imprimirCarne')->name('nfe.imprimir-carne');

Route::get('nfe/set-codigo-unico/{id}', 'NfeController@setCodigoUnico')->name('nfe.set-codigo-unico');
Route::post('nfe/setar-codigo-unico', 'NfeController@setarCodigoUnico')->name('nfe.setar-codigo-unico');
Route::get('nfe-danfe-temporaria/{id}', 'NfeController@danfeTemporaria')->name('nfe.danfe-temporaria');
Route::get('nfe/danfe-simples/{id}', 'NfeController@danfeSimples')->name('nfe.danfe-simples');
Route::get('nfe/danfe-etiqueta/{id}', 'NfeController@danfeEtiqueta')->name('nfe.danfe-etiqueta');
Route::get('nfe/imprimir-cancela/{id}', 'NfeController@imprimirCancela')->name('nfe.imprimir-cancela');
Route::get('nfe/imprimir-correcao/{id}', 'NfeController@imprimirCorrecao')->name('nfe.imprimir-correcao');
Route::get('nfe/xml-temp/{id}', 'NfeController@xmlTemp')->name('nfe.xml-temp');
Route::get('nfe-import-zip', 'NfeController@importZip')->name('nfe.import-zip');
Route::post('nfe-import-zip-store', 'NfeController@importZipStore')->name('nfe.import-zip-store');
Route::post('nfe-import-zip-store-files', 'NfeController@importZipStoreFiles')->name('nfe.import-zip-store-files');
Route::get('/nfe-duplicar/{id}', 'NfeController@duplicar')->name('nfe.duplicar');
Route::post('nfe-send-email', 'NfeController@sendEmail')->name('nfe.send-email');
Route::get('nfe-metas', 'NfeController@metas')->name('nfe.metas');
Route::get('nfe-sieg-teste', 'NfeController@siegTeste');
Route::get('asaas-teste', 'AsaasController@index');

Route::get('nfce-import-zip', 'NfceController@importZip')->name('nfce.import-zip');
Route::post('nfce-import-zip-store', 'NfceController@importZipStore')->name('nfce.import-zip-store');
Route::post('nfce-import-zip-store-files', 'NfceController@importZipStoreFiles')->name('nfce.import-zip-store-files');

Route::get('nfce/imprimir/{id}', 'NfceController@imprimir')->name('nfce.imprimir');
Route::get('nfce-danfce-temporaria/{id}', 'NfceController@danfceTemporaria')->name('nfce.danfce-temporaria');

Route::get('nfe/alterar-estado/{id}', 'NfeController@alterarEstado')->name('nfe.alterar-estado');
Route::put('nfe/storeEstado/{id}', 'NfeController@storeEstado')->name('nfe.storeEstado');
Route::get('nfe/imprimirVenda/{id}', 'NfeController@imprimirVenda')->name('nfe.imprimirVenda');
Route::get('nfe-download-xml/{id}', 'NfeController@downloadXml')->name('nfe.download-xml');

Route::resource('nfce-contigencia', 'NfceContigenciaController');

Route::get('nfce/xml-temp/{id}', 'NfceController@xmlTemp')->name('nfce.xml-temp');
Route::get('nfce/xml-temp-contigencia/{id}', 'NfceController@xmlTempContigencia')->name('nfce.xml-temp-contigencia');
Route::get('nfce-download-xml/{id}', 'NfceController@downloadXml')->name('nfce.download-xml');
Route::get('cte/xml-temp/{id}', 'CteController@xmlTemp')->name('cte.xml-temp');
Route::get('cte/imprimir/{id}', 'CteController@imprimir')->name('cte.imprimir');
Route::get('cte/download/{id}', 'CteController@download')->name('cte.download');
Route::get('cte/imprimir-cancela/{id}', 'CteController@imprimirCancela')->name('cte.imprimir-cancela');
Route::get('cte/imprimir-correcao/{id}', 'CteController@imprimirCorrecao')->name('cte.imprimir-correcao');

Route::get('cte-os/xml-temp/{id}', 'CteOsController@xmlTemp')->name('cte-os.xml-temp');
Route::get('cte-os/alterar-estado/{id}', 'CteOsController@alterarEstado')->name('cte-os.alterar-estado');
Route::put('cte-os/storeEstado/{id}', 'CteOsController@storeEstado')->name('cte-os.storeEstado');
Route::get('cte-os/imprimir/{id}', 'CteOsController@imprimir')->name('cte-os.imprimir');
Route::get('cte-os/download/{id}', 'CteOsController@download')->name('cte-os.download');
Route::get('cte-os/imprimir-cancela/{id}', 'CteOsController@imprimirCancela')->name('cte-os.imprimir-cancela');

Route::get('mdfe/xml-temp/{id}', 'MdfeController@xmlTemp')->name('mdfe.xml-temp');
Route::get('mdfe/damdfe-temp/{id}', 'MdfeController@damdfeTemp')->name('mdfe.damdfe-temporaria');
Route::get('mdfe/imprimir/{id}', 'MdfeController@imprimir')->name('mdfe.imprimir');
Route::get('mdfe/download/{id}', 'MdfeController@download')->name('mdfe.download');
Route::get('mdfe/imprimir-cancela/{id}', 'MdfeController@imprimirCancela')->name('mdfe.imprimir-cancela');
Route::get('mdfe/imprimir-correcao/{id}', 'MdfeController@imprimirCorrecao')->name('mdfe.imprimir-correcao');
Route::get('mdfe/nao-encerrados', 'MdfeController@naoEncerrados')->name('mdfe.nao-encerrados');
Route::get('mdfe/encerrar', 'MdfeController@encerrar')->name('mdfe.encerrar');
Route::get('mdfe/create-by-vendas/{id}', 'MdfeController@createByVendas')->name('mdfe.create.vendas');
Route::get('mdfe-duplicar/{id}', 'MdfeController@duplicar')->name('mdfe.duplicar');


    // Route::group(['prefix' => 'empresas'], function () {
    //     Route::get('/config/{id}', 'EmpresaController@config')->name('empresas.config');
    //     Route::get('/painel', 'EmpresaController@painel')->name('empresas.painel');
    // });

Route::get('mercado-livre-get-code', 'MercadoLivreAuthController@getCode')->name('mercado-livre.get-code');
Route::get('mercado-livre-auth-code', 'MercadoLivreAuthController@authCode')->name('mercado-livre.auth');
Route::get('mercado-livre-auth-token', 'MercadoLivreAuthController@authToken')->name('mercado-livre.auth-token');
Route::get('mercado-livre-refresh-token', 'MercadoLivreAuthController@refreshToken')->name('mercado-livre.refresh-token');
Route::get('mercado-livre-get-users', 'MercadoLivreAuthController@getUsers')->name('mercado-livre.get-users');
Route::resource('mercado-livre-config', 'MercadoLivreConfigController');
Route::get('mercado-livre-categorias', 'MercadoLivreProdutoController@categorias')->name('mercado-livre.categorias');
Route::get('mercado-livre-produtos-news', 'MercadoLivreProdutoController@produtosNew')->name('mercado-livre.produtos-news');
Route::get('mercado-livre-produtos-galery/{id}', 'MercadoLivreProdutoController@galery')->name('mercado-livre-produtos.galery');
Route::post('mercado-livre-produtos-galery-store', 'MercadoLivreProdutoController@galeryStore')->name('mercado-livre-produtos-galery-store');
Route::get('mercado-livre-produtos-galery-delete', 'MercadoLivreProdutoController@galeryDelete')->name('mercado-livre-produtos.galery-delete');
Route::resource('mercado-livre-produtos', 'MercadoLivreProdutoController');
Route::resource('mercado-livre-perguntas', 'MercadoLivrePerguntaController');
Route::resource('mercado-livre-pedidos', 'PedidoMercadoLivreController');
Route::get('mercado-livre-nfe/{id}', 'PedidoMercadoLivreController@gerarNfe')->name('mercado-livre-pedidos.gerar-nfe');
Route::put('mercado-livre-pedido-set-cliente/{id}', 'PedidoMercadoLivreController@setCliente')->name('mercado-livre-pedidos.set-cliente');

Route::get('mercado-livre-chat/{id}', 'PedidoMercadoLivreController@chat')->name('mercado-livre-pedidos.chat');
Route::get('mercado-livre-chat-download/{id}', 'PedidoMercadoLivreController@downloadChat')->name('mercado-livre-pedidos.chat-download');
Route::post('mercado-livre-chat-send/{id}', 'PedidoMercadoLivreController@chatSend')->name('mercado-livre-chat.send');
Route::get('mercado-livre-chat-send-nfe/{id}', 'PedidoMercadoLivreController@chatSendNfe')->name('mercado-livre-chat.send-nfe');

Route::resource('upgrade', 'UpgradePlanoController');
Route::get('upgrade-assinatura', 'UpgradePlanoController@assinatura')->name('upgrade.assinatura');
// Route::post('upgrade-assinatura-mercado-pago', 'UpgradePlanoController@assinaturaMercadoPago')->name('upgrade-assinatura.mercado-pago');

Route::resource('plano-contas', 'PlanoContaController');
Route::post('plano-contas-start', 'PlanoContaController@start')->name('plano-contas.start');

Route::resource('nuvem-shop-auth', 'NuvemShopAuthController');
Route::get('nuvem-shop-auth-code', 'NuvemShopAuthController@code');
Route::resource('nuvem-shop-config', 'NuvemShopConfigController');
Route::resource('nuvem-shop-categorias', 'NuvemShopCategoriaController');
Route::resource('nuvem-shop-pedidos', 'NuvemShopPedidoController');
Route::resource('nuvem-shop-produtos', 'NuvemShopProdutoController');
Route::get('nuvem-shop-gerar-nfe/{id}', 'NuvemShopPedidoController@gerarNfe')->name('nuvem-shop-pedidos.gerar-nfe');
Route::get('nuvem-shop-produtos-galery/{id}', 'NuvemShopProdutoController@galery')->name('nuvem-shop-produtos.galery');
Route::get('nuvem-shop-produtos-galery-delete', 'NuvemShopProdutoController@galeryDelete')->name('nuvem-shop-produtos.galery-delete');
Route::post('nuvem-shop-produtos-galery-store', 'NuvemShopProdutoController@galeryStore')
->name('nuvem-shop-produtos-galery-store');

Route::resource('woocommerce-config', 'WoocommerceConfigController');
Route::resource('woocommerce-categorias', 'WoocommerceCategoriaController');
Route::resource('woocommerce-produtos', 'WoocommerceProdutoController');
Route::get('woocommerce-produtos-galery/{id}', 'WoocommerceProdutoController@galery')->name('woocommerce-produtos.galery');
Route::get('woocommerce-produtos-sem-cadastro', 'WoocommerceProdutoController@semCadastro')->name('woocommerce-produtos.sem-cadastro');
Route::post('woocommerce-produtos-sincronizar', 'WoocommerceProdutoController@sincronizar')->name('woocommerce-produtos.sincronizar');

Route::post('woocommerce-produtos-galery-store', 'WoocommerceProdutoController@galeryStore')->name('woocommerce-produtos-galery-store');
Route::get('woocommerce-produtos-galery-delete', 'WoocommerceProdutoController@galeryDelete')->name('woocommerce-produtos.galery-delete');
Route::resource('woocommerce-pedidos', 'WoocommercePedidoController');
Route::put('woocommerce-pedido-set-cliente/{id}', 'WoocommercePedidoController@setCliente')->name('woocommerce-pedidos.set-cliente');
Route::get('woocommerce-gerar-nfe/{id}', 'WoocommercePedidoController@gerarNfe')->name('woocommerce-pedidos.gerar-nfe');

Route::resource('vendas', 'VendaController');
Route::resource('vendizap-config', 'VendiZapConfigController');
Route::resource('vendizap-categorias', 'VendiZapCategoriaController');
Route::resource('vendizap-produtos', 'VendiZapProdutoController');
Route::resource('vendizap-pedidos', 'VendiZapPedidoController');
Route::get('vendizap-gerar-nfe/{id}', 'VendiZapPedidoController@gerarNfe')->name('vendizap-pedidos.gerar-nfe');
Route::put('vendizap-set-cliente/{id}', 'VendiZapPedidoController@setCliente')->name('vendizap-pedidos.set-cliente');
Route::post('vendizap-store-cliente/{id}', 'VendiZapPedidoController@storeCliente')->name('vendizap-pedidos.store-cliente');
Route::post('vendizap-update-tributacao/{pedido_id}', 'VendiZapPedidoController@updateTributacao')->name('vendizap-pedidos.update-tributacao');

Route::get('vendizap-teste', 'VendiZapProdutoController@teste');
Route::get('teste', 'TesteController@index');

Route::resource('projeto-custo', 'ProjetoCustoController');
Route::get('projeto-custo-print/{id}', 'ProjetoCustoController@print')->name('projeto-custo.print');

Route::resource('planejamento-custo', 'PlanejamentoCustoController');
Route::resource('gestao-producao', 'GestaoProducaoController');
Route::put('gestao-producao-finish/{id}', 'GestaoProducaoController@finish')->name('gestao-producao.finish');

Route::post('planejamento-custo-alterar-estado/{id}', 'PlanejamentoCustoController@alerarEstado')->name('planejamento-custo.alterar-estado');
Route::post('planejamento-custo-store-cotacao/{id}', 'PlanejamentoCustoController@storeCotacao')->name('planejamento-custo.store-cotacao');
Route::get('planejamento-custo-cotacao{id}', 'PlanejamentoCustoController@cotacao')->name('planejamento-custo.cotacao');
Route::get('planejamento-custo-imprimir-proposta/{id}', 'PlanejamentoCustoController@imprimirProposta')
->name('planejamento-custo.imprimir-proposta');

Route::get('planejamento-custo-proposta/{id}', 'PlanejamentoCustoController@criarProposta')->name('planejamento-custo.proposta');
Route::post('planejamento-custo-proposta-store/{id}', 'PlanejamentoCustoController@propostaStore')->name('planejamento-custo.proposta-store');
Route::get('planejamento-custo-preview/{id}', 'PlanejamentoCustoController@preview')->name('planejamento-custo.preview');

Route::resource('contas-empresa', 'ContaEmpresaController');
Route::get('contas-empresa-print/{id}', 'ContaEmpresaController@print')->name('contas-empresa.print');

Route::resource('suporte', 'SuporteController')->middleware('validaSuporte');
Route::resource('contas-boleto', 'ContaBoletoController');
Route::resource('remessa-boleto', 'RemessaBoletoController');
Route::get('remessa-boleto-download/{id}', 'RemessaBoletoController@download')->name('remessa-boleto.download');
Route::get('remessa-boleto-import', 'RemessaBoletoController@import')->name('remessa-boleto.import');
Route::post('remessa-boleto-import-store', 'RemessaBoletoController@importStore')->name('remessa-boleto.import-store');
Route::post('remessa-boleto-import-save', 'RemessaBoletoController@importSave')->name('remessa-boleto.import-save');

Route::get('boletos', 'BoletoController@index')->name('boleto.index');
Route::get('boleto-show/{id}', 'BoletoController@show')->name('boleto.show');
Route::get('boleto-print/{id}', 'BoletoController@print')->name('boleto.print');
Route::get('boleto-create/{conta_id}', 'BoletoController@create')->name('boleto.create');
Route::get('boleto-create-several', 'BoletoController@createSeveral')->name('boleto.create-several');
Route::post('boleto-store', 'BoletoController@store')->name('boleto.store');
Route::delete('boleto-destroy/{id}', 'BoletoController@destroy')->name('boleto.destroy');

Route::resource('ticket', 'TicketController');
Route::put('ticket-add-mensagem/{id}', 'TicketController@addMensagem')->name('ticket.add-mensagem');

Route::resource('cash-back-config', 'CashBackConfigController');
Route::resource('tef-config', 'TefConfigController');
Route::resource('tef-registros', 'TefRegistroController');
Route::resource('usuario-super', 'UsuarioSuperController');

Route::get('impersonate/{id}', 'ImpersonateController@start')->name('impersonate.start')->middleware('impersonate');
Route::get('impersonate-stop', 'ImpersonateController@stop')->name('impersonate.stop');

Route::get('impersonate-contador/{id}', 'ImpersonateContadorController@start')->name('impersonate-contador.start')->middleware('impersonateContador');
Route::get('impersonate-contador-stop', 'ImpersonateContadorController@stop')->name('impersonate-contador.stop');

Route::get('produto-tributacao-local/{id}', 'ProdutoTributacaoLocalController@index')->name('produto-tributacao-local.index');
Route::put('produto-tributacao-local/{id}', 'ProdutoTributacaoLocalController@update')->name('produto-tributacao-local.update');

Route::get('teste-dimensao', 'TefController@index');
Route::get('teste-tef', 'TefController@teste');
Route::get('tef-imprimir/{venda_id}', 'TefController@imprimir');
Route::resource('email-config', 'EmailConfigController');
Route::resource('escritorio-contabil', 'EscritorioContabilController');
Route::resource('importador', 'ImportadorController');

Route::resource('sped-config', 'SpedConfigController');
Route::resource('sped', 'SpedController');
Route::resource('sintegra', 'SintegraController');
Route::resource('crm', 'CrmController');
Route::post('crm-store-nota/{id}', 'CrmController@storeNota')->name('crm-nota.store');
Route::delete('crm-destroy-nota/{id}', 'CrmController@destroyNota')->name('crm.destroy-nota');
Route::get('crm-print', 'CrmController@print')->name('crm.print');

Route::resource('mensagem-padrao-crm', 'MensagemPadraoCrmController');
Route::resource('mensagem-crm-logs', 'MensagemCrmLogController');

Route::middleware(['verificaEmpresa', 'validaPlano', 'validaContrato'])->group(function () {
    Route::get('teste-email', 'EmailController@index')->name('teste-email');
    Route::post('teste-email-send', 'EmailController@send')->name('teste-email-send');

    Route::resource('nfe', 'NfeController');
    Route::resource('nfe-xml', 'NfeXmlController');
    Route::get('nfe-xml-download', 'NfeXmlController@download')->name('nfe-xml.download');
    Route::get('nfe-xml-envio-contador', 'NfeXmlController@enviarContador')->name('nfe-xml.envio-contador');

    Route::resource('nfe-entrada-xml', 'NfeEntradaXmlController');
    Route::get('nfe-entrada-xml-download', 'NfeEntradaXmlController@download')->name('nfe-entrada-xml.download');
    Route::get('nfe-entrada-xml-envio-contador', 'NfeEntradaXmlController@enviarContador')->name('nfe-entrada-xml.envio-contador');

    Route::resource('nfe-importa-xml', 'NfeImportaXmlController');
    Route::get('nfe-importa-xml-download', 'NfeImportaXmlController@download')->name('nfe-importa-xml.download');
    Route::get('nfe-importa-xml-envio-contador', 'NfeImportaXmlController@enviarContador')->name('nfe-importa-xml.envio-contador');

    Route::resource('nfce-xml', 'NfceXmlController');
    Route::get('nfce-xml-download', 'NfceXmlController@download')->name('nfce-xml.download');
    Route::get('nfce-xml-envio-contador', 'NfceXmlController@enviarContador')->name('nfce-xml.envio-contador');

    Route::resource('cte-xml', 'CteXmlController');
    Route::get('cte-xml-download', 'CteXmlController@download')->name('cte-xml.download');

    Route::resource('mdfe-xml', 'MdfeXmlController');
    Route::get('mdfe-xml-download', 'MdfeXmlController@download')->name('mdfe-xml.download');

    Route::get('nfe-inutilizar', 'NfeController@inutilizar')->name('nfe.inutilizar');
    Route::post('nfe-inutilizar-store', 'NfeController@inutilStore')->name('nfe-inutilizar.store');
    Route::post('nfce-inutilizar-store', 'NfceController@inutilStore')->name('nfce-inutilizar.store');
    Route::delete('nfe-inutilizar-destroy/{id}', 'NfeController@inutilDestroy')->name('nfe-inutilizar.destroy');
    Route::get('nfce-inutilizar', 'NfceController@inutilizar')->name('nfce.inutilizar');
    Route::get('nfce/alterar-estado/{id}', 'NfceController@alterarEstado')->name('nfce.alterar-estado');
    Route::put('nfce/storeEstado/{id}', 'NfceController@storeEstado')->name('nfce.storeEstado');

    Route::resource('nfce', 'NfceController');
    Route::resource('controle-acesso', 'ControleAcessoController');

    Route::get('/compras/info-validade/{id}', 'CompraController@infoValidade')->name('compras.info-validade');
    Route::post('/compras/setar-info-validade', 'CompraController@setarInfoValidade')->name('compras.setar-info-validade');

    Route::get('/compras-inteligencia', 'ComprasIAController@dashboard')->name('compras-inteligencia.dashboard');
    Route::post('/compras/inteligencia/analisar', 'ComprasIAController@analisar')->name('compras.ia.analisar');

    Route::get('/compras/set-codigo-unico/{id}', 'CompraController@setCodigoUnico')->name('compras.set-codigo-unico');
    Route::post('/compras/setar-codigo-unico', 'CompraController@setarCodigoUnico')->name('compras.setar-codigo-unico');

    Route::get('/compras-etiqueta/{id}', 'CompraController@etiqueta')->name('compras.etiqueta');
    Route::get('/compras-download-xml-importado/{id}', 'CompraController@downloadXmlImportado')->name('compras.download-xml-importado');
    Route::post('/compras-etiqueta-store/{id}', 'CompraController@etiquetaStore')->name('compras.etiqueta-store');

    Route::resource('compras', 'CompraController');
    Route::resource('relacao-dados-fornecedor', 'RelacaoDadosFornecedorController');
    Route::resource('cotacoes', 'CotacaoController');
    Route::get('/compras-purchase/{id}', 'CotacaoController@purchase')->name('cotacoes.purchase');

    Route::get('/compras-validade', 'CompraController@validade')->name('compras.validade');
    Route::get('/compras-xml', 'CompraController@xml')->name('compras.xml');
    Route::post('/store-xml', 'CompraController@storeXml')->name('compras.store-xml');
    Route::post('/compras-finish-xml', 'CompraController@finishXml')->name('compras.finish-xml');

    Route::resource('devolucao', 'DevolucaoController');
    Route::resource('localizacao', 'LocalizacaoController');
    Route::get('localizacao-delete-logo/{id}', 'LocalizacaoController@removerLogo')->name('localizacao.delete-logo');
    
    Route::get('/devolucao-xml', 'DevolucaoController@xml')->name('devolucao.xml');
    Route::post('/devolucao-store-xml', 'DevolucaoController@storeXml')->name('devolucao.store-xml');
    Route::post('/devolucao-finish-xml', 'DevolucaoController@finishXml')->name('devolucao.finish-xml');

    Route::group(['prefix' => 'manifesto'], function () {
        Route::get('/novaConsulta', 'ManifestoController@novaconsulta')->name('manifesto.novaConsulta');
        Route::get('/download/{id}', 'ManifestoController@download')->name('manifesto.download');
        Route::get('/danfe/{id}', 'ManifestoController@danfe')->name('manifesto.danfe');
        Route::post('/manifestar', 'ManifestoController@manifestar')->name('manifesto.manifestar');
    });
    Route::resource('manifesto', 'ManifestoController');
    Route::resource('impressoras-pedido', 'ImpressoraPedidoController');
    Route::resource('impressao-pedido', 'ImpressaoPedidoController');
    Route::get('impressao-pedido-comando', 'ImpressaoPedidoController@comando');

    Route::resource('ifood-config', 'IfoodConfigController');
    Route::resource('ifood-catalogos', 'IfoodCatalogoController');
    Route::resource('ifood-categoria-produtos', 'IfoodCategoriaProdutoController');
    Route::resource('ifood-produtos', 'IfoodProdutoController');
    Route::get('/ifood-catalogos-definir/{id}', 'IfoodCatalogoController@definir')->name('ifood-catalogos.definir');

    Route::resource('ifood-config-loja', 'IfoodConfigLojaController');
    Route::get('/ifood-config-user-code', 'IfoodConfigController@userCode')->name('ifood-config.user-code');
    Route::get('/ifood-config-get-token', 'IfoodConfigController@getToken')->name('ifood-config.get-token');

    Route::resource('nota-servico', 'NotaServicoController');
    Route::resource('nota-servico-config', 'NotaServicoConfigController');
    Route::get('nota-servico-config-certificado', 'NotaServicoConfigController@certificado')->name('nota-servico-config.certificado');
    Route::post('nota-servico-config-upload-certificado', 'NotaServicoConfigController@uploadCertificado')->name('nota-servico-config.upload-certificado');

    Route::get('nota-servico-imprimir/{id}', 'NotaServicoController@imprimir')->name('nota-servico.imprimir');
    Route::get('nota-servico-preview/{id}', 'NotaServicoController@preview')->name('nota-servico.preview');
    Route::get('nota-servico-preview-xml/{id}', 'NotaServicoController@previewXml')->name('nota-servico.preview-xml');

    Route::get('cte-inutilizar', 'CteController@inutilizar')->name('cte.inutilizar');
    Route::post('cte-inutilizar-store', 'CteController@inutilStore')->name('cte-inutilizar.store');
    Route::delete('cte-inutilizar-destroy/{id}', 'CteController@inutilDestroy')->name('cte-inutilizar.destroy');
    Route::get('cte/alterar-estado/{id}', 'CteController@alterarEstado')->name('cte.alterar-estado');
    Route::put('cte/storeEstado/{id}', 'CteController@storeEstado')->name('cte.storeEstado');
    Route::resource('cte', 'CteController');

    Route::resource('tipo-despesa-frete', 'TipoDespesaFreteController');
    Route::resource('fretes', 'FreteController');
    Route::put('fretes-alterar-estado/{id}', 'FreteController@alterarEstado')->name('fretes.alterar-estado');
    Route::get('fretes-gerar-conta-receber/{id}', 'FreteController@gerarContaReceber')->name('fretes.gerar-conta-receber');
    Route::post('fretes-upload/{id}', 'FreteController@upload')->name('fretes.upload');
    Route::delete('fretes-destroy-file/{id}', 'FreteController@destroyFile')->name('fretes.destroy-file');
    Route::get('despesa-frete-gerar-conta-pagar/{id}', 'FreteController@gerarContaPagar')->name('despesa-frete.gerar-conta-pagar');

    Route::resource('garantias', 'GarantiaController');
    Route::get('garantias-modal/{id}', 'GarantiaController@modal');
    Route::get('garantias/imprimir/{id}', 'GarantiaController@imprimir');
    
    Route::resource('manutencao-veiculos', 'ManutencaoVeiculoController');
    Route::put('manutencao-veiculos-alterar-estado/{id}', 'ManutencaoVeiculoController@alterarEstado')->name('manutencao-veiculos.alterar-estado');
    Route::get('manutencao-veiculos-gerar-conta-pagar/{id}', 'ManutencaoVeiculoController@gerarContaReceber')->name('manutencao-veiculos.gerar-conta-pagar');

    Route::post('manutencao-veiculos-upload/{id}', 'ManutencaoVeiculoController@upload')->name('manutencao-veiculos.upload');
    Route::delete('manutencao-veiculos-destroy-file/{id}', 'ManutencaoVeiculoController@destroyFile')->name('manutencao-veiculos.destroy-file');

    Route::resource('veiculos', 'VeiculoController');
    Route::resource('contigencia', 'ContigenciaController');
    Route::get('contigencia-desactive/{id}', 'ContigenciaController@desactive')->name('contigencia.desactive');

    Route::resource('cte-os', 'CteOsController');
    Route::resource('cupom-desconto', 'CupomDescontoController');
    Route::resource('bairros-empresa', 'BairroEmpresaController');
    Route::get('bairros-empresa-super', 'BairroEmpresaController@super')->name('bairros-empresa.super');
    Route::post('bairros-empresa-super', 'BairroEmpresaController@setBairros')->name('bairros-empresa.super');

    Route::resource('mdfe', 'MdfeController');
    Route::get('mdfe-inutilizar', 'MdfeController@inutilizar')->name('mdfe.inutilizar');
    Route::post('mdfe-inutilizar-store', 'MdfeController@inutilStore')->name('mdfe-inutilizar.store');
    Route::delete('mdfe-inutilizar-destroy/{id}', 'MdfeController@inutilDestroy')->name('mdfe-inutilizar.destroy');
    Route::get('mdfe/alterar-estado/{id}', 'MdfeController@alterarEstado')->name('mdfe.alterar-estado');
    Route::put('mdfe/storeEstado/{id}', 'MdfeController@storeEstado')->name('mdfe.storeEstado');

    Route::resource('clientes', 'ClienteController');
    Route::delete('clientes-destroy-select', 'ClienteController@destroySelecet')->name('clientes.destroy-select');
    Route::get('clientes-cash-back/{id}', 'ClienteController@cashBack')->name('clientes.cash-back');
    Route::get('clientes-import', 'ClienteController@import')->name('clientes.import');
    Route::get('clientes-historico/{id}', 'ClienteController@historico')->name('clientes.historico');
    Route::get('clientes-import-download', 'ClienteController@downloadModelo')->name('clientes.import-download');
    Route::post('clientes-import-store', 'ClienteController@storeModelo')->name('clientes.import-store');

    Route::get('/clientes-remove-image/{id}', 'ClienteController@removeImagem')->name('clientes.remove-image');
    Route::get('clientes-modal/{id}', 'ClienteController@modal');

    Route::post('clientes-creditos/alterar-status', 'ClienteController@alterarStatusCredito')->name('clientes.alterar-status-credito');

    Route::resource('fornecedores', 'FornecedorController');
    Route::delete('fornecedores-destroy-select', 'FornecedorController@destroySelecet')->name('fornecedores.destroy-select');

    Route::get('fornecedores-import', 'FornecedorController@import')->name('fornecedores.import');
    Route::get('fornecedores-import-download', 'FornecedorController@downloadModelo')
    ->name('fornecedores.import-download');
    Route::post('fornecedores-import-store', 'FornecedorController@storeModelo')->name('fornecedores.import-store');
    Route::get('fornecedores-historico/{id}', 'FornecedorController@historico')->name('fornecedores.historico');

    Route::get('produtos-gerar-codigo-ean', 'ProdutoController@gerarCodigoEan');
    Route::get('produtos-reajuste', 'ProdutoController@reajuste')->name('produtos.reajuste');
    Route::get('produtos-upload-imagens', 'ProdutoController@uploadImagens')->name('produtos.upload-imagens');
    Route::post('produtos-reajuste-update', 'ProdutoController@reajusteUpdate')->name('produtos-reajuste.update');
    Route::post('produtos-upload-multiple', 'ProdutoController@uploadMultiple')->name('produtos.upload-multiple');
    Route::post('produtos-vincular-imagens', 'ProdutoController@vincularImagens')->name('produtos.vincular-imagens');
    Route::get('produtos-vincula-imagens', 'ProdutoController@vinculaImagens')->name('produtos.vincula-imagens');
    Route::get('produtos-ibpt', 'ProdutoController@ibpt')->name('produtos.ibpt');
    Route::get('produtos-alterar-valor-estoque', 'ProdutoController@alterarValorEstoque')->name('produtos.alterar-valor-estoque');
    Route::get('produtos-buscar-ajuste', 'ProdutoController@buscarAjuste')->name('produtos.buscar-ajuste');
    Route::post('/produtos/alterar-campo', 'ProdutoController@alterarCampo')->name('produtos.alterar-campo');

    Route::resource('transportadoras', 'TransportadoraController');
    Route::delete('transportadoras-destroy-select', 'TransportadoraController@destroySelecet')->name('transportadoras.destroy-select');

    Route::resource('transferencia-estoque', 'TransferenciaEstoqueController');
    Route::get('transferencia-estoque-imprimir/{id}', 'TransferenciaEstoqueController@imprimir')->name('transferencia-estoque.imprimir');

    Route::resource('inventarios', 'InventarioController');
    Route::get('inventarios-apontar/{id}', 'InventarioController@apontar')->name('inventarios.apontar');
    Route::post('store-item/{id}', 'InventarioController@storeItem')->name('inventarios.store-item');
    Route::get('inventarios-itens/{id}', 'InventarioController@itens')->name('inventarios.itens');
    Route::get('inventarios-comparar-estoque/{id}', 'InventarioController@compararEstoque')->name('inventarios.comparar-estoque');
    Route::delete('inventarios-destroy-item/{id}', 'InventarioController@destroyItem')->name('inventarios.destroy-item');
    Route::get('inventarios-definir-estoque/{id}', 'InventarioController@definirEstoque')->name('inventarios.definir-estoque');
    Route::get('inventarios-imprimir/{id}', 'InventarioController@imprimir')->name('inventarios.imprimir');
    Route::get('inventarios-apontar-impresso/{id}', 'InventarioController@apontarImpresso')->name('inventarios.apontar-impresso');
    Route::post('/inventario/atualizar-item', 'InventarioController@atualizarItem')->name('inventario.atualizar-item');
    Route::post('/inventario-acerto', 'InventarioController@acerto')->name('inventario.acerto');
    Route::get('inventarios-duplicar/{id}', 'InventarioController@duplicar')->name('inventarios.duplicar');
    Route::get('inventarios-renderizar/{id}', 'InventarioController@renderizar')->name('inventarios.renderizar');
    Route::post('/inventarios-add-produto', 'InventarioController@addProduto')->name('inventarios.add-produto');

    Route::post('/inventarios-delete-item', 'InventarioController@deleteItem')->name('inventarios.delete-item');

    Route::resource('estoque', 'EstoqueController');
    Route::get('estoque-retirada', 'EstoqueController@retirada')->name('estoque.retirada');
    Route::post('estoque-retirada-store', 'EstoqueController@retiradaStore')->name('estoque-retirada.store');
    Route::delete('estoque-retirada-destroy/{id}', 'EstoqueController@retiradaDestroy')->name('estoque-retirada.destroy');
    Route::get('estoque-localizacao-define/{id}', 'EstoqueLocalizacaoController@define')->name('estoque-localizacao.define');
    Route::put('estoque-localizacao-store/{id}', 'EstoqueLocalizacaoController@store')->name('estoque-localizacao.store');
    Route::resource('categoria-produtos', 'CategoriaProdutoController');
    Route::resource('unidades-medida', 'UnidadeMedidaController');
    Route::delete('categoria-produtos-destroy-select', 'CategoriaProdutoController@destroySelecet')->name('categoria-produtos.destroy-select');

    Route::resource('produtos', 'ProdutoController');
    Route::delete('produtos-destroy-select', 'ProdutoController@destroySelecet')->name('produtos.destroy-select');
    Route::post('produtos-desactive-select', 'ProdutoController@desactiveSelecet')->name('produtos.desactive-select');

    Route::resource('variacoes', 'VariacaoController');
    Route::delete('variacoes-destroy-select', 'VariacaoController@destroySelecet')->name('variacoes.destroy-select');

    Route::resource('promocao-produtos', 'PromocaoProdutoController');
    Route::get('/promocao-produtos-group', 'PromocaoProdutoController@group')->name('promocao-produtos.group');
    Route::post('/promocao-produtos-set-group', 'PromocaoProdutoController@setGroup')->name('promocao-produtos.set-group');

    Route::get('/movimentacao/{id}', 'ProdutoController@movimentacao')->name('produtos.movimentacao');
    Route::get('/duplicar/{id}', 'ProdutoController@duplicar')->name('produtos.duplicar');
    Route::get('/remove-image/{id}', 'ProdutoController@removeImagem')->name('produtos.remove-image');
    Route::get('/produtos-galeria/{id}', 'ProdutoController@galeria')->name('produtos.galeria');
    Route::post('/produtos-galeria-store/{id}', 'ProdutoController@storeImage')->name('produtos.galeria-store');
    Route::delete('/produtos-galeria-destroy/{id}', 'ProdutoController@destroyImage')->name('produtos.destroy-image');

    Route::get('/produto-find-ml/{id}', 'ProdutoController@findProdutoMercadoLivre');
    Route::get('/produto-etiqueta/{id}', 'ProdutoController@etiqueta')->name('produtos.etiqueta');
    Route::post('/produto-etiqueta-store/{id}', 'ProdutoController@etiquetaStore')->name('produtos.etiqueta-store');

    Route::resource('marcas', 'MarcaController');
    Route::resource('modelo-etiquetas', 'ModeloEtiquetaController');
    Route::get('/modelo-etiquetas-importar', 'ModeloEtiquetaController@importar')->name('modelo-etiquetas.import');

    Route::delete('marcas-destroy-select', 'MarcaController@destroySelecet')->name('marcas.destroy-select');

    Route::group(['prefix' => 'produto-composto'], function () {
        Route::get('/create/{id}', 'ProdutoCompostoController@create')->name('produto-composto.create');
            // Route::get('/create_item/{id}', 'ProdutoController@createItem')->name('produtosComposto.create_item');
        Route::post('/store/{id}', 'ProdutoCompostoController@store')->name('produto-composto.store');
        Route::delete('/destroy/{id}', 'ProdutoCompostoController@destroy')->name('produto-composto.destroy');
        Route::get('/show/{id}', 'ProdutoCompostoController@show')->name('produto-composto.show');
    });

    Route::resource('apontamento', 'ApontamentoController');
    // Route::resource('assinar-contrato', 'AssinarContratoController');
    Route::resource('assinar-contrato', 'AssinarContratoController')->withoutMiddleware('validaContrato');

    Route::get('/imprimir-apontamento/{id}', 'ApontamentoController@imprimir')->name('apontamento.imprimir');
    
    Route::resource('produto-consulta-codigo', 'ProdutoConsultaCodigoController');

    Route::get('produtos-import', 'ProdutoController@import')->name('produtos.import');
    Route::get('produtos-export', 'ProdutoController@export')->name('produtos.export');
    Route::get('produtos-tamanho-pizza/{id}', 'ProdutoCardapioController@tamanhosPizza')->name('produtos.tamanho-pizza');
    Route::put('produtos-tamanho-pizza/{id}', 'ProdutoCardapioController@setValoresTamnho')->name('produtos.setar-tamanhos-valores');
    Route::get('produtos-import-download', 'ProdutoController@downloadModelo')->name('produtos.import-download');
    Route::post('produtos-import-store', 'ProdutoController@storeModelo')->name('produtos.import-store');

    Route::resource('produtopadrao-tributacao', 'PadraoTributacaoProdutoController');
    Route::delete('produtopadrao-tributacao-destroy-select', 'PadraoTributacaoProdutoController@destroySelecet')->name('produtopadrao-tributacao.destroy-select');

    Route::get('produtopadrao-tributacao-alterar', 'PadraoTributacaoProdutoController@alterarProdutos')
    ->name('produtopadrao-tributacao.alterar');
    Route::post('produtopadrao-tributacao-set-tributacao', 'PadraoTributacaoProdutoController@setTributacao')
    ->name('produtopadrao-tributacao.set-tributacao');

    Route::group(['prefix' => 'funcionarios'], function () {
        Route::get('/atribuir/{id}', 'FuncionarioController@atribuir')->name('funcionarios.atribuir');
        Route::post('/atribuir-servico', 'FuncionarioController@atribuirServico')->name('funcionarios.atribuir-servico');
        Route::delete('/deletarAtribuicao/{id}', 'FuncionarioController@deletarAtribuicao')->name('funcionarios.deletarAtribuicao');
    });

    Route::resource('funcionarios', 'FuncionarioController');
    Route::resource('lista-preco', 'ListaPrecoController');
    Route::delete('lista-preco-destroy-select', 'ListaPrecoController@destroySelecet')->name('lista-preco.destroy-select');
    Route::post('/lista-preco-update-item', 'ListaPrecoController@updateItem')->name('lista-preco.update-item');

    Route::resource('evento-funcionarios', 'EventoFuncionarioController');
    Route::delete('evento-funcionarios-destroy-select', 'EventoFuncionarioController@destroySelecet')->name('evento-funcionarios.destroy-select');

    Route::resource('funcionario-eventos', 'FuncionarioEventoController');
    Route::resource('apuracao-mensal', 'ApuracaoMensalController');

    Route::get('/apuracao-mensal/get-eventos/{funcionario_id}', 'ApuracaoMensalController@getEventos')->name('apuracao-mensal.get-eventos');
    Route::get('/apuracao-mensal/conta-pagar/{apuracao_id}', 'ApuracaoMensalController@contaPagar')->name('apuracao-mensal.conta-pagar');

    Route::put('/apuracao-mensal/SetConta/{id}', 'ApuracaoMensalController@setConta')->name('apuracao-mensal.set-conta');

    Route::group(['prefix' => 'usuarios'], function () {
        Route::get('profile/{id}', 'UsuarioController@profile')->name('usuarios.profile');
        Route::get('alterar-senha/{id}', 'UsuarioController@alterSenha')->name('usuarios.alterar-senha');
        Route::put('update-senha/{id}', 'UsuarioController@updateSenha')->name('usuarios.update-senha');
    });

    Route::resource('usuarios', 'UsuarioController');
    Route::resource('natureza-operacao', 'NaturezaOperacaoController');
    Route::resource('conta-pagar', 'ContaPagarController');
    Route::delete('conta-pagar-destroy-select', 'ContaPagarController@destroySelecet')->name('conta-pagar.destroy-select');
    Route::post('conta-pagar-pagar-select', 'ContaPagarController@pagarSelecionados')->name('conta-pagar.pagar-select');
    Route::get('conta-pagar-download-file/{id}', 'ContaPagarController@downloadFile')->name('conta-pagar.download-file');
    Route::get('conta-pagar-estornar/{id}', 'ContaPagarController@estornar')->name('conta-pagar.estornar');
    Route::put('conta-pagar-estornar-update/{id}', 'ContaPagarController@estornarUpdate')->name('conta-pagar.estornar-update');
    Route::get('conta-pagar-export-excel', 'ContaPagarController@exportExcel')->name('conta-pagar.export-excel');
    Route::put('conta-pagar-pay-select', 'ContaPagarController@paySelect')->name('conta-pagar.pay-select');
    
    Route::resource('conta-receber', 'ContaReceberController');

    Route::get('/financeiro-dashboard', 'FinanceiroDashboardController@index')->name('financeiro.dashboard');

    Route::delete('conta-receber-destroy-select', 'ContaReceberController@destroySelecet')->name('conta-receber.destroy-select');
    Route::get('conta-receber-download-file/{id}', 'ContaReceberController@downloadFile')->name('conta-receber.download-file');
    Route::get('conta-receber-imprimir-comprovante/{id}', 'ContaReceberController@imprimirComprovante')->name('conta-receber.imprimir-comprovante');
    Route::get('conta-receber-estornar/{id}', 'ContaReceberController@estornar')->name('conta-receber.estornar');
    Route::get('conta-receber-export-excel', 'ContaReceberController@exportExcel')->name('conta-receber.export-excel');
    Route::put('conta-receber-estornar-update/{id}', 'ContaReceberController@estornarUpdate')->name('conta-receber.estornar-update');
    Route::post('conta-receber-recebe-select', 'ContaReceberController@receberSelecionados')->name('conta-receber.recebe-select');
    Route::put('conta-receber-receive-select', 'ContaReceberController@receiveSelect')->name('conta-receber.receive-select');

    Route::resource('categoria-conta', 'CategoriaContaController');

    Route::resource('produtos-cardapio', 'ProdutoCardapioController');
    Route::resource('produtos-delivery', 'ProdutoDeliveryController');
    Route::resource('servicos-marketplace', 'ServicoMarketPlaceController');
    Route::resource('produtos-reserva', 'ProdutoReservaController');
    Route::resource('produtos-ecommerce', 'ProdutoEcommerceController');
    Route::resource('pedidos-ecommerce', 'PedidoEcommerceController');

    Route::get('/pedidos-ecommerce-alterar-estado/{id}', 'PedidoEcommerceController@alterarEstado')->name('pedidos-ecommerce.alterar-estado');
    Route::get('/pedidos-ecommerce-nfe/{id}', 'PedidoEcommerceController@gerarNfe')->name('pedidos-ecommerce.gerar-nfe');

    Route::resource('clientes-delivery', 'ClienteDeliveryController');
    Route::get('/clientes-delivery-enderecos/{id}', 'ClienteDeliveryController@enderecos')
    ->name('clientes-delivery.enderecos');

    Route::resource('notificacao', 'NotificacaoController');
    Route::get('notificacao-clear-all', 'NotificacaoController@clearAll')->name('notificacao.clear-all');

    Route::resource('config-cardapio', 'ConfigCardapioController');
    Route::get('config-cardapio-download', 'ConfigCardapioController@download')->name('config-cardapio.download');

    Route::resource('atendimento-garcom', 'AtendimentoGarcomController');
    Route::resource('config-marketplace', 'MarketPlaceConfigController');
    Route::get('/config-marketplace-loja', 'MarketPlaceConfigController@verLoja')
    ->name('config-marketplace.loja');
    Route::resource('config-ecommerce', 'EcommerceConfigController');
    Route::get('/config-ecommerce-site', 'EcommerceConfigController@verSite')
    ->name('config-ecommerce.site');

    Route::get('/produtos-cardapio-ingredientes/{id}', 'ProdutoCardapioController@ingredientes')
    ->name('produtos-cardapio.ingredientes');

    Route::resource('avaliacao-cardapio', 'AvaliacaoCardapioController');
    Route::resource('tamanhos-pizza', 'TamanhoPizzaController');
    Route::resource('carrossel', 'CarrosselCardapioController');
    Route::resource('destaque-marketplace', 'DestaqueMarketPlaceController');
    Route::resource('motoboys', 'MotoboyController');
    Route::get('/motoboys-comissao', 'MotoboyController@comissao')->name('motoboys-comissao.index');
    Route::post('/motoboys-comissao-pay-multiple', 'MotoboyController@payMultiple')->name('motoboys-comissao.pay-multiple');
    Route::resource('pedidos-cardapio', 'PedidoCardapioController');
    Route::get('pedidos-cardapio-historico', 'PedidoCardapioController@historico')->name('pedidos-cardapio.historico');
    Route::get('/pedidos-cardapio-liberar/{id}', 'PedidoCardapioController@liberarMesa')->name('pedidos-cardapio.liberar');
    Route::delete('/pedidos-cardapio-delete', 'PedidoCardapioController@delete')->name('pedidos-cardapio.delete');
    Route::put('/pedidos-cardapio-update-table/{id}', 'PedidoCardapioController@updateTable')->name('pedidos-cardapio.update-table');

    Route::resource('mesas', 'MesaController');
    Route::get('/mesas-qr-code/{id}', 'MesaController@qrCode')->name('mesas.qr-code');

    Route::resource('pedido-cozinha', 'PedidoCozinhaController');

    Route::resource('pedidos-delivery', 'PedidoDeliveryController');
    Route::get('/pedido-delivery-altera-status', 'PedidoDeliveryController@alteraStatus')->name('pedido-delivery.altera-status');
    Route::post('/pedidos-delivery-store-item/{id}', 'PedidoDeliveryController@storeItem')->name('pedidos-delivery.store-item');
    Route::get('/pedidos-delivery-print/{id}', 'PedidoDeliveryController@print')->name('pedidos-delivery.print');
    Route::delete('/pedidos-delivery-destroy-item/{id}', 'PedidoDeliveryController@destroyItem')
    ->name('pedidos-delivery.destroy-item');
    Route::get('/pedidos-delivery-finish/{id}', 'PedidoDeliveryController@finish')->name('pedidos-delivery.finish');
    Route::post('/pedidos-delivery-set-endereco/{id}', 'PedidoDeliveryController@setEndereco')
    ->name('pedidos-delivery.set-endereco');
    Route::post('/pedidos-delivery-store-endereco/{id}', 'PedidoDeliveryController@storeEndereco')
    ->name('pedidos-delivery.store-endereco');
    Route::get('pedidos-delivery-update-item/{id}', 'PedidoDeliveryController@updateItem')->name('pedidos-delivery.update-item');
    Route::get('pedidos-delivery-enviar-mensagem-wpp/{id}', 'PedidoDeliveryController@enviarMensagemWpp')->name('pedidos-delivery.enviar-wpp');

    Route::prefix('balanca')->group(function () {
        Route::get('/', 'ExportarBalancaController@index')->name('balanca.index');
        Route::get('toledo-mgv5', 'ExportarBalancaController@exp_bal_toledo_mgv5')->name('balanca.toledo_mgv5');
        Route::get('toledo-mgv6', 'ExportarBalancaController@exp_bal_toledo_mgv6')->name('balanca.toledo_mgv6');
        Route::get('filizola/{arquivo?}', 'ExportarBalancaController@exportarFilizola')->name('balanca.filizola');
    });

    Route::get('/pedido-cozinha-update-item/{id}', 'PedidoCozinhaController@updateItem')->name('pedido-cozinha.update-item');
    Route::get('/pedido-cozinha-update-all', 'PedidoCozinhaController@updateAll')->name('pedido-cozinha.update-all');
    Route::get('/pedidos-cardapio-finish/{id}', 'PedidoCardapioController@finish')->name('pedidos-cardapio.finish');
    Route::get('/pedidos-cardapio-finish-client', 'PedidoCardapioController@finishClient')->name('pedidos-cardapio.finish-client');
    Route::get('/pedidos-cardapio-print/{id}', 'PedidoCardapioController@print')->name('pedidos-cardapio.print');
    Route::get('/pedidos-cardapio-print-html/{id}', 'PedidoCardapioController@printHtml')->name('pedidos-cardapio.print-html');
    Route::post('/pedidos-cardapio-store-item/{id}', 'PedidoCardapioController@storeItem')->name('pedidos-cardapio.store-item');
    Route::post('/pedidos-cardapio-store-servico/{id}', 'PedidoCardapioController@storeServico')->name('pedidos-cardapio.store-servico');
    Route::delete('/pedidos-cardapio-destroy-item/{id}', 'PedidoCardapioController@destroyItem')
    ->name('pedidos-cardapio.destroy-item');
    Route::delete('/pedidos-cardapio-destroy-item-servico/{id}', 'PedidoCardapioController@destroyItemServico')
    ->name('pedidos-cardapio.destroy-item-servico');

    Route::resource('adicionais', 'AdicionalController');
    Route::resource('categoria-adicional', 'CategoriaAdicionalController');
    Route::get('/produtos-cardapio-categorias', 'ProdutoCardapioController@categorias')
    ->name('produtos-cardapio.categorias');
    Route::get('/produtos-delivery-categorias', 'ProdutoDeliveryController@categorias')
    ->name('produtos-delivery.categorias');

    Route::get('/categoria-servico-marketplace', 'ServicoMarketPlaceController@categorias')
    ->name('servico-marketplace.categorias');

    Route::get('/produtos-ecommerce-categorias', 'ProdutoEcommerceController@categorias')
    ->name('produtos-ecommerce.categorias');

    Route::post('/produtos-cardapio-store-adicional', 'ProdutoCardapioController@storeAdicional')
    ->name('produtos-cardapio.store-adicional');
    Route::post('/produtos-cardapio-store-ingrediente', 'ProdutoCardapioController@storeIngrediente')
    ->name('produtos-cardapio.store-ingrediente');
    Route::delete('/produtos-cardapio-destroy/{id}', 'ProdutoCardapioController@destroyAdicional')
    ->name('produtos-cardapio.destroy-adicional');
    Route::delete('/produtos-cardapio-ingrediente/{id}', 'ProdutoCardapioController@destroyIngrediente')
    ->name('produtos-cardapio.destroy-ingrediente');

    Route::group(['prefix' => 'caixa'], function () {
        Route::post('/fechar', 'CaixaController@fechar')->name('caixa.fechar');
        Route::get('/fechar-empresa/{id}', 'CaixaController@fecharEmpresa')->name('caixa.fechar-empresa');
        Route::get('/list', 'CaixaController@list')->name('caixa.list');
        Route::get('/imprimir/{id}', 'CaixaController@imprimir')->name('caixa.imprimir');
        Route::get('/imprimir80/{id}', 'CaixaController@imprimir80')->name('caixa.imprimir80');
        Route::get('/fechar-conta/{id}', 'CaixaController@fecharConta')->name('caixa.fechar-conta');
        Route::post('/fechar-conta/{id}', 'CaixaController@fecharTiposPagamento')->name('caixa.fechar-tipos-pagamento');
        Route::get('/abertos-empresa', 'CaixaController@abertosEmpresa')->name('caixa.abertos-empresa');
    });

    Route::resource('caixa', 'CaixaController');

    Route::resource('sangria', 'SangriaController');
    Route::get('/sangria-print/{id}', 'SangriaController@print')->name('sangria.print');
    Route::get('/suprimento-print/{id}', 'SuprimentoController@print')->name('suprimento.print');

    Route::resource('suprimento', 'SuprimentoController');

    Route::resource('categoria-servico', 'CategoriaServicoController');
    Route::delete('categoria-servico-destroy-select', 'CategoriaServicoController@destroySelecet')->name('categoria-servico.destroy-select');

    Route::resource('servicos', 'ServicoController');
    Route::delete('servicos-destroy-select', 'ServicoController@destroySelecet')->name('servicos.destroy-select');

    Route::resource('atendimentos', 'AtendimentoController');

    Route::resource('interrupcoes', 'InterrupcoesController');
    Route::group(['prefix' => 'interrupcoes'], function () {
        Route::get('/register/{id}', 'InterrupcoesController@register')->name('interrupcoes.register');
    });

    Route::resource('orcamentos', 'OrcamentoController');
    Route::group(['prefix' => 'orcamentos'], function () {
        Route::get('imprimir/{id}', 'OrcamentoController@imprimir')->name('orcamentos.imprimir');
        Route::get('gerar-venda/{id}', 'OrcamentoController@gerarVenda')->name('orcamentos.gerar-venda');
        Route::get('gerar-venda-nfce/{id}', 'OrcamentoController@gerarVendaNfce')->name('orcamentos.gerar-venda-nfce');
    });
    Route::get('orcamentos-gerar-venda-multipla', 'OrcamentoController@gerarVendaMultipla')->name('orcamentos.gerar-venda-multipla');

    Route::group(['prefix' => 'conta-receber'], function () {
        Route::get('/{id}/pay', 'ContaReceberController@pay')->name('conta-receber.pay');
        Route::put('/{id}/pay-put', 'ContaReceberController@payPut')->name('conta-receber.pay-put');
    });

    Route::group(['prefix' => 'conta-pagar'], function () {
        Route::get('/{id}/pay', 'ContaPagarController@pay')->name('conta-pagar.pay');
        Route::put('/{id}/pay-put', 'ContaPagarController@payPut')->name('conta-pagar.pay-put');
    });

    Route::group(['prefix' => 'frontbox'], function () {
        Route::get('/imprimir-nao-fiscal/{id}', 'FrontBoxController@imprimirNaoFiscal')->name('frontbox.imprimir-nao-fiscal');
        Route::get('/imprimir-a4/{id}', 'FrontBoxController@imprimirA4')->name('frontbox.imprimir-a4');
        Route::get('/imprimir-nao-fiscal-html/{id}', 'FrontBoxController@imprimirNaoFiscalHtml')->name('frontbox.imprimir-nao-fiscal-html');
        Route::get('/destroy-suspensa/{id}', 'FrontBoxController@destroySuspensa')->name('frontbox.destroy-suspensa');
        Route::post('/definir-mesa', 'FrontBoxController@definirMesa')->name('frontbox.definir-mesa');
        Route::get('/imprimir-carne/{id}', 'FrontBoxController@imprimirCarne')->name('frontbox.imprimir-carne');

        Route::get('/logs', 'FrontBoxController@logs')->name('frontbox.logs');
    });

    Route::resource('frontbox', 'FrontBoxController');
    Route::get('/frontbox-mesas', 'FrontBoxController@mesas')->name('frontbox.mesas');
    Route::get('/frontbox-teste', 'FrontBoxController@teste')->name('frontbox.teste');

    Route::resource('trocas', 'TrocaController');
    Route::get('/trocas/imprimir/{codigo}', 'TrocaController@imprimir')->name('trocas.imprimir');

    Route::resource('pre-venda', 'PreVendaController');
    Route::get('/pre-venda/imprimir/{codigo}', 'PreVendaController@imprimir')->name('pre-venda.imprimir');

    Route::resource('convenios', 'ConvenioController');
    Route::resource('medicos', 'MedicoController');
    Route::resource('laboratorios', 'LaboratorioController');
    Route::resource('tipo-armacao', 'TipoArmacaoController');
    Route::resource('tratamentos-otica', 'TratamentoOticaController');
    Route::resource('formato-armacao', 'FormatoArmacaoOticaController');
    Route::resource('config-fiscal-usuario', 'UsuarioEmissaoController');
    Route::resource('metas', 'MetaResultadoController');

    Route::group(['prefix' => 'ordem-servico'], function () {
        Route::post('/store-servico', 'OrdemServicoController@storeServico')->name('ordem-servico.store-servico');
        Route::post('/store-produto', 'OrdemServicoController@storeProduto')->name('ordem-servico.store-produto');
        Route::delete('/delete-produto/{id}', 'OrdemServicoController@deletarProduto')->name('ordem-servico.deletar-produto');
        Route::delete('/deletar-servico/{id}', 'OrdemServicoController@deletarServico')->name('ordem-servico.deletar-servico');

        Route::get('/alterar-estado/{id}', 'OrdemServicoController@alterarEstado')->name('ordem-servico.alterar-estado');
        Route::get('/duplicar/{id}', 'OrdemServicoController@duplicar')->name('ordem-servico.duplicar');
        Route::post('/update-estado/{id}', 'OrdemServicoController@updateEstado')->name('ordem-servico.update-estado');
        Route::get('/imprimir/{id}', 'OrdemServicoController@imprimir')->name('ordem-servico.imprimir');
        Route::post('/store-funcionario', 'OrdemServicoController@storeFuncionario')->name('ordem-servico.store-funcionario');
        Route::get('/add-relatorio/{id}', 'OrdemServicoController@addRelatorio')->name('ordem-servico.add-relatorio');
        Route::get('/alterar-status-servico/{id}', 'OrdemServicoController@alterarStatusServico')->name('ordem-servico.alterar-status-servico');
        Route::delete('/delete-funcionario/{id}', 'OrdemServicoController@deleteFuncionario')->name('ordem-servico.deleteFuncionario');
        Route::post('/store-relatorio', 'OrdemServicoController@storeRelatorio')->name('ordem-servico.store-relatorio');
        Route::delete('/delete-relatorio/{id}', 'OrdemServicoController@deleteRelatorio')->name('ordem-servico.delete-relatorio');
        Route::get('/edit-relatorio/{id}', 'OrdemServicoController@editRelatorio')->name('ordem-servico.edit-relatorio');
        Route::post('/alterar-estado-post', 'OrdemServicoController@alterarEstadoPost')->name('ordem-servico.alterar-estado-post');

        Route::put('/update-relatorio/{id}', 'OrdemServicoController@updateRelatorio')->name('ordem-servico.update-relatorio');
        Route::get('/gerar-nfe/{id}', 'OrdemServicoController@gerarNfe')->name('ordem-servico.gerar-nfe');
        Route::get('/print-otica', 'OrdemServicoController@printOtica')->name('ordem-servico.print-otica');
        Route::put('/update-entrega/{id}', 'OrdemServicoController@updateEntrega')->name('ordem-servico.update-entrega');
        Route::put('/update-descricao/{id}', 'OrdemServicoController@updateDescricao')->name('ordem-servico.update-descricao');
        Route::get('/ordem-servico-download-arquivo/{id}', 'OrdemServicoController@downloadArquivo')->name('ordem-servico.download-arquivo');
        Route::get('/metas', 'OrdemServicoController@metas')->name('ordem-servico.metas');

        Route::put('/update-diagnostico/{id}', 'OrdemServicoController@updateDiagnostico')->name('ordem-servico.update-diagnostico');

    });

    Route::resource('taxa-cartao', 'TaxaCartaoController');

    Route::resource('ordem-servico', 'OrdemServicoController');
    Route::resource('ordem-producao', 'OrdemProducaoController');
    Route::put('/ordem-producao-update-estado/{id}', 'OrdemProducaoController@updateEstado')->name('ordem-producao.update-estado');
    Route::get('/ordem-producao-status-item/{id}', 'OrdemProducaoController@alterarStatusItem')->name('ordem-producao-status-item');
    Route::get('/ordem-producao-imprimir-etiquetas/{id}', 'OrdemProducaoController@imprimirEtiquetas')->name('ordem-producao.imprimir-etiquetas');
    Route::get('/ordem-producao-imprimir/{id}', 'OrdemProducaoController@imprimir')->name('ordem-producao.imprimir');
    Route::post('/ordem-producao-config', 'OrdemProducaoController@config')->name('ordem-producao.config');

    Route::delete('ordem-servico-destroy-select', 'OrdemServicoController@destroySelecet')->name('ordem-servico.destroy-select');

    Route::resource('agendamentos', 'AgendamentoController');
    Route::resource('config-agendamento', 'ConfiguracaoAgendamentoController');
    Route::get('/config-agendamento-logs-message', 'ConfiguracaoAgendamentoController@logs')->name('config-agendamento.logs-message');

    Route::post('/config-agendamento-teste-wpp', 'ConfiguracaoAgendamentoController@testeWpp')->name('config.agendamento.teste-wpp');

    Route::put('agendamentos-update-status/{id}', 'AgendamentoController@updateStatus')->name('agendamentos.update-status');
    Route::get('/agendamentos-pdv/{id}', 'AgendamentoController@pdv')->name('agendamentos.pdv');

    Route::resource('config-reserva', 'ConfigReservaController');
    Route::resource('categoria-acomodacao', 'CategoriaAcomodacaoController');
    Route::resource('acomodacao', 'AcomodacaoController');
    Route::resource('frigobar', 'FrigobarController');
    Route::resource('reservas', 'ReservaController');
    Route::get('/reservas-checkin/{id}', 'ReservaController@checkin')->name('reservas.checkin');
    Route::get('/reservas-imprimir/{id}', 'ReservaController@imprimir')->name('reservas.imprimir');
    Route::post('/reservas-checkin/{id}', 'ReservaController@checkinStart')->name('reservas.checkin-start');
    Route::post('/reservas-store-produto/{id}', 'ReservaController@storeProduto')->name('reservas.store-produto');
    Route::post('/reservas-store-servico/{id}', 'ReservaController@storeServico')->name('reservas.store-servico');
    Route::post('/reservas-store-nota/{id}', 'ReservaController@storeNota')->name('reservas.store-nota');
    Route::post('/reservas-cancelamento/{id}', 'ReservaController@cancelamento')->name('reservas.cancelamento');
    Route::put('/reservas-update-hospedes/{id}', 'ReservaController@updateHospedes')->name('reservas.update-hospedes');
    Route::delete('/reservas-destroy-produto/{id}', 'ReservaController@destroyProduto')->name('reservas.destroy-produto');
    Route::delete('/reservas-destroy-servico/{id}', 'ReservaController@destroyServico')->name('reservas.destroy-servico');
    Route::delete('/reservas-destroy-nota/{id}', 'ReservaController@destroyNota')->name('reservas.destroy-nota');
    Route::post('/reservas-store-fatura/{id}', 'ReservaController@storeFatura')->name('reservas.store-fatura');
    Route::get('reservas-gerar-nfe/{id}', 'ReservaController@gerarNfe')->name('reservas.gerar-nfe');
    Route::get('reservas-gerar-nfse/{id}', 'ReservaController@gerarNfse')->name('reservas.gerar-nfse');
    Route::get('reservas-conferir-frigobar/{id}', 'ReservaController@conferirFrigobar')->name('reservas.conferir-frigobar');
    Route::put('reservas-update-estado/{id}', 'ReservaController@updateEstado')->name('reservas.update-estado');

    Route::post('/frigobar-store-default/{id}', 'FrigobarController@storeDefault')->name('frigobar.store-default');
    Route::post('/reservas-conferir-frigobar-save/{id}', 'ReservaController@conferenciaFrigobarSave')->name('reservas.conferir-frigobar-save');

    Route::resource('funcionamentos', 'FuncionamentoController');
    Route::resource('funcionamento-delivery', 'FuncionamentoDeliveryController');
    Route::resource('difal', 'DifalController');
    Route::resource('comissao-margem', 'MargemComissaoController');
    Route::resource('comissao', 'ComissaoController');
    Route::post('/comissao-pay-multiple', 'ComissaoController@payMultiple')->name('comissao.pay-multiple');

    Route::resource('pdv-mobo', 'PdvMoboController');

    Route::group(['prefix' => 'relatorios'], function () {
        Route::get('/', 'RelatorioController@index')->name('relatorios.index');
        Route::get('relatorio-produtos', 'RelatorioController@produtos')->name('relatorios.produtos');
        Route::get('relatorio-clientes', 'RelatorioController@clientes')->name('relatorios.clientes');
        Route::get('relatorio-fornecedores', 'RelatorioController@fornecedores')->name('relatorios.fornecedores');
        Route::get('relatorio-nfe', 'RelatorioController@nfe')->name('relatorios.nfe');
        Route::get('relatorio-nfce', 'RelatorioController@nfce')->name('relatorios.nfce');
        Route::get('relatorio-cte', 'RelatorioController@cte')->name('relatorios.cte');
        Route::get('relatorio-mdfe', 'RelatorioController@mdfe')->name('relatorios.mdfe');
        Route::get('relatorio-conta_pagar', 'RelatorioController@conta_pagar')->name('relatorios.conta_pagar');
        Route::get('relatorio-conta_receber', 'RelatorioController@conta_receber')->name('relatorios.conta_receber');
        Route::get('relatorio-comissao', 'RelatorioController@comissao')->name('relatorios.comissao');
        Route::get('relatorio-vendas', 'RelatorioController@vendas')->name('relatorios.vendas');
        Route::get('relatorio-compras', 'RelatorioController@compras')->name('relatorios.compras');
        Route::get('relatorio-taxas', 'RelatorioController@taxas')->name('relatorios.taxas');
        Route::get('relatorio-lucro', 'RelatorioController@lucro')->name('relatorios.lucro');
        Route::get('relatorio-venda-produtos', 'RelatorioController@vendaProdutos')->name('relatorios.venda-produtos');
        Route::get('relatorio-estoque', 'RelatorioController@estoque')->name('relatorios.estoque');
        Route::get('relatorio-despesa-frete', 'RelatorioController@despesaFrete')->name('relatorios.despesa-frete');
        Route::get('totaliza-produtos', 'RelatorioController@totalizaProdutos')->name('relatorios.totaliza-produtos');
        Route::get('custo-medio', 'RelatorioController@custoMedio')->name('relatorios.inventario-custo-medio');
        Route::get('inventario', 'RelatorioController@inventario')->name('relatorios.inventario');
        Route::get('curva-abc-clientes', 'RelatorioController@curvaAbcClientes')->name('relatorios.curva-abc-clientes');
        Route::get('entrega-produtos', 'RelatorioController@entregaDeProdutos')->name('relatorios.entrega-produtos');
        Route::get('venda-por-vendedor', 'RelatorioController@vendasPorVendedor')->name('relatorios.venda-por-vendedor');
        Route::get('movimentacao', 'RelatorioController@movimentacao')->name('relatorios.movimentacao');
        Route::get('ordem-servico', 'RelatorioController@ordemServico')->name('relatorios.ordem-servico');
        Route::get('tipos-pagamento', 'RelatorioController@tiposDePagamento')->name('relatorios.tipos-pagamento');
        Route::get('reservas', 'RelatorioController@reservas')->name('relatorios.reservas');
        Route::get('lucro-produto', 'RelatorioController@lucroProduto')->name('relatorios.lucro-produto');
        Route::get('registro-inventario', 'RelatorioController@registroInventario')->name('relatorios.registro-inventario');
    });

    Route::resource('config-geral', 'ConfigGeralController');
    Route::resource('config-api', 'ConfigApiController');
    Route::get('config-api-logs', 'ConfigApiController@logs')->name('config-api.logs');

});
Route::resource('config', 'ConfigController');
Route::get('config-delete-logo', 'ConfigController@removerLogo')->name('config.delete-logo');
Route::resource('minhas-faturas', 'MinhaFaturaController');

Route::resource('payment', 'PaymentController');
Route::get('payment/pix/{transacao_id}', 'PaymentController@pix')->name('payment.pix');
Route::get('payment-asaas/{plano_id}', 'PaymentController@asaas')->name('payment.asaas');
Route::get('xml-download/{chave}', 'DownloadXmlController@download');
});
