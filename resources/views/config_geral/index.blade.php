@extends('layouts.app', ['title' => 'Configurações Gerais'])
@section('css')
<style type="text/css">
    .select2-container--default .select2-selection__clear {
        font-size: 22px !important;
        color: #a94442;
        margin-right: 35px !important;
        cursor: pointer;
        font-weight: bold;
    }
</style>
@endsection
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Configuração Geral</h4>
                <hr>
                <div class="row mt-3">
                    <div class="col-lg-12">
                        {!!Form::open()->fill($item)
                        ->post()
                        ->route('config-geral.store')
                        ->multipart()
                        !!}
                        <div class="m-2">
                            <h5 class="card-header bg-primary text-white">PDV</h5>
                            <div class="card-body">
                                <div class="row g-1">
                                    <div class="col-md-3">
                                        {!!Form::text('balanca_digito_verificador', 'Referência produto balança (dígitos)')->value(isset($item) ? $item->balanca_digito_verificador : '')
                                        !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!!Form::select('balanca_valor_peso', 'Tipo unidade balança', ['valor' => 'Valor', 'peso' => 'Peso'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!!Form::select('abrir_modal_cartao', 'Abrir modal dados do cartão', ['1' => 'Sim', '0' => 'Não'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>
                                    <div class="col-md-3">
                                        {!!Form::text('senha_manipula_valor', 'Senha para desconto/acréscimo/remover item')                                        
                                        !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!!Form::select('agrupar_itens', 'Agrupar itens', ['0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('tipo_comissao', 'Tipo de comissão', ['percentual_vendedor' => '% Vendedor', 'percentual_margem' => '% Margem'])->attrs(['class' => 'form-select tooltipp3'])
                                        !!}
                                        <div class="text-tooltip3 d-none">
                                            Marcar como sim se for usar esta categoria no cardápio
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('modelo', 'Modelo', ['light' => 'Light', 'compact' => 'Compact', 'quick' => 'Quick'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('alerta_sonoro', 'Alerta sonoro', ['1' => 'Sim', '0' => 'Não'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('cabecalho_pdv', 'Cabeçalho no PDV', ['1' => 'Sim', '0' => 'Não'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('definir_vendedor_pdv', 'Definir vendedor', ['0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!!Form::select('impressao_sem_janela_cupom', 'Impressão sem janela', ['0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select tooltipp2'])
                                        !!}
                                        <div class="text-tooltip2 d-none">
                                            Marcar como sim e utilizar firefox com a propriedade print.always_print_silent como verdadeiro, ou o chrome em modo Kiosk
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('documento_pdv', 'Documento PDV', ['nfce' => 'NFCe', 'nfe' => 'NFe'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::text('numero_inicial_comanda', 'Númeração inicial para comanda')                                        
                                        !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!!Form::text('numero_final_comanda', 'Númeração final para comanda')                                        
                                        !!}
                                    </div>

                                    <div class="row mt-2">
                                        <h5>Tipos de pagamento</h5>
                                        
                                        @foreach(\App\Models\Nfce::tiposPagamento() as $key => $t)
                                        <div class="col-lg-3 col-6">
                                            <input name="tipos_pagamento_pdv[]" value="{{$t}}" type="checkbox" class="form-check-input check-module" style=" width: 25px; height: 25px;" @isset($item) @if(sizeof($item->tipos_pagamento_pdv) > 0 && in_array($t, $item->tipos_pagamento_pdv)) checked="true" @endif @endif>
                                            <label class="form-check-label m-1" for="customCheck1">{{$t}}</label>
                                        </div>
                                        @endforeach
                                    </div>

                                    <hr>
                                    <div class="row mt-2">
                                        <h5 class="col-12">Pagamento PIX Mercado Pago</h5>
                                        <div class="col-md-6">
                                            {!!Form::text('mercadopago_public_key_pix', 'Public Key')
                                            !!}
                                        </div>
                                        <div class="col-md-6">
                                            {!!Form::text('mercadopago_access_token_pix', 'Access Token')
                                            !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">Venda</h5>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        {!!Form::select('gerar_conta_receber_padrao', 'Gerar conta receber padrão', ['1' => 'Sim', '0' => 'Não'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('limitar_credito_cliente', 'Limitar crédito do cliente', [ '0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-12">
                                        {!!Form::textarea('mensagem_padrao_impressao_venda', 'Mensagem padrão impressão')->attrs(['class' => 'tiny', 'rows' => '5'])
                                        !!}
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">Compra</h5>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        {!!Form::select('gerar_conta_pagar_padrao', 'Gerar conta pagar padrão', ['1' => 'Sim', '0' => 'Não'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">Ordem de Serviço</h5>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        {!!Form::select('tipo_ordem_servico', 'Tipo da OS', \App\Models\OrdemServico::tiposDeOrdemServico())
                                        ->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-12">
                                        {!!Form::textarea('mensagem_padrao_impressao_os', 'Mensagem padrão impressão')->attrs(['class' => 'tiny', 'rows' => '5'])
                                        !!}
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">Pré venda</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        {!!Form::select('confirmar_itens_prevenda', 'Confirmar itens pré venda', ['0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">Orçamento</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        {!!Form::tel('percentual_desconto_orcamento', '% Máximo de desconto sobre lucro')
                                        ->attrs(['class' => 'percentual'])
                                        !!}
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">Produto</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        {!!Form::tel('percentual_lucro_produto', '% Lucro padrão')
                                        ->attrs(['class' => 'percentual'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::tel('margem_combo', 'Margem % combo')
                                        ->attrs(['class' => 'percentual'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        <label class="form-label">Gerenciar estoque</label>
                                        <div class="input-group input-group-merge" style="margin-top: -2px">
                                            <select class="form-select" name="gerenciar_estoque" id="inp-gerenciar_estoque">
                                                <option @if($item && $item->gerenciar_estoque == 0) selected @endif value="0">Não</option>
                                                <option @if($item && $item->gerenciar_estoque == 1) selected @endif value="1">Sim</option>
                                            </select>
                                            <div class="input-group-text">
                                                <span onclick="alterarParaTodosEstoque()">
                                                    Alterar
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">Alertas</h5>
                            <div class="card-body">

                                <div class="row m-3">
                                    @foreach(App\Models\ConfigGeral::getNotificacoes() as $n)
                                    <div class="col-lg-3 col-6">
                                        <input name="notificacoes[]" value="{{$n}}" type="checkbox" class="form-check-input" style=" width: 25px; height: 25px;" @isset($item) @if(sizeof($item->notificacoes) > 0 && in_array($n, $item->notificacoes)) checked="true" @endif @endif>
                                        <label class="form-check-label m-1" for="customCheck1">{{$n}}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">NFSe</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        {!!Form::select('regime_nfse', 'Regime NFSe', App\Models\ConfigGeral::tributacoesNfse())->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">PDV Off-line</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-2">
                                        {!!Form::select('definir_vendedor_pdv_off', 'Definir vendedor', ['0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>
                                    <div class="col-md-4">
                                        {!!Form::select('cliente_padrao_pdv_off', 'Cliente padrão')
                                        ->options(isset($item) && $item->cliente_padrao_pdv_off ? [$item->cliente_padrao_pdv_off => $item->cliente->info] : [])
                                        ->attrs(['class' => 'inp-cliente_id'])
                                        !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!!Form::select('alterar_valor_pdv_off', 'Alterar valor', ['1' => 'Sim', '0' => 'Não', ])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="row mt-2">
                                        <h5>Acessos do PDV</h5>
                                        
                                        @foreach(\App\Models\ConfigGeral::acessosPdvOff() as $key => $t)
                                        <div class="col-lg-3 col-6">
                                            <input name="acessos_pdv_off[]" value="{{$t}}" type="checkbox" class="form-check-input check-module" style=" width: 25px; height: 25px;" @isset($item) @if(sizeof($item->acessos_pdv_off) > 0 && in_array($t, $item->acessos_pdv_off)) checked="true" @endif @endif>
                                            <label class="form-check-label m-1" for="customCheck1">{{$t}}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">Configuração</h5>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        {!!Form::select('tipo_menu', 'Tipo do menu', ['vertical' => 'Vertical', 'horizontal' => 'Horizontal'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('cor_menu', 'Cor do menu', ['light' => 'Light', 'brand' => 'Brand', 'dark' => 'Dark'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('cor_top_bar', 'Cor da barra superior', ['light' => 'Light', 'brand' => 'Brand', 'dark' => 'Dark'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('usar_ibpt', 'Usar IBPT', ['1' => 'Sim', '0' => 'Não'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('casas_decimais_quantidade', 'Casas decimais para quantidade', ['2' => '2', '3' => '3', '4' => '4'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::tel('ultimo_codigo_produto', 'Último código para produtos')
                                        ->attrs(['data-mask' => '00000000'])
                                        !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!!Form::tel('ultimo_codigo_cliente', 'Último código para clientes')
                                        ->attrs(['data-mask' => '00000000'])
                                        !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!!Form::tel('ultimo_codigo_fornecedor', 'Último código para fornecedores')
                                        ->attrs(['data-mask' => '00000000'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('corrigir_numeracao_fiscal', 'Corrigir sequencia de número fiscal', ['1' => 'Sim', '0' => 'Não'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-6">
                                        {!!Form::text('token_whatsapp', 'Token WhatsApp')
                                        !!}
                                        <h5 class="text-success mt-1">Token https://criarwhats.com</h5>
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::text('itens_por_pagina', 'Itens por página')
                                        !!}
                                    </div>

                                    <div class="col-md-3">
                                        <label>Background menu lateral</label>
                                        <select name="small_header_user" class="form-control select2">
                                            @foreach($smallImages as $i)
                                            <option @if(isset($item) && $item->small_header_user == $i->getFilename()) selected @endif value="{{ $i->getFilename() }}" data-image="{{ '/assets/images/small/' . $i->getFilename() }}">{{ $i->getFilename() }}</option>
                                            @endforeach

                                        </select>
                                    </div>
                                    <div class="col-12"></div>

                                    <div class="col-md-2">
                                        <label>Exibir produtos:</label>
                                        <div class="">
                                            <div class="form-check">
                                                <input @if(isset($item) && $item->produtos_exibe_tabela == 1) checked @endif type="radio" id="customRadio1" name="produtos_exibe_tabela" value="1" class="form-check-input">
                                                <label class="form-check-label" for="customRadio1">Tabela</label>
                                            </div>
                                            <div class="form-check">
                                                <input @if(isset($item) && $item->produtos_exibe_tabela == 0) checked @endif type="radio" id="customRadio2" name="produtos_exibe_tabela" value="0" class="form-check-input">
                                                <label class="form-check-label" for="customRadio2">Card</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <label>Exibir clientes:</label>
                                        <div class="">
                                            <div class="form-check">
                                                <input @if(isset($item) && $item->clientes_exibe_tabela == 1) checked @endif type="radio" id="customRadioCliente1" name="clientes_exibe_tabela" value="1" class="form-check-input">
                                                <label class="form-check-label" for="customRadioCliente1">Tabela</label>
                                            </div>
                                            <div class="form-check">
                                                <input @if(isset($item) && $item->clientes_exibe_tabela == 0) checked @endif type="radio" id="customRadioCliente2" name="clientes_exibe_tabela" value="0" class="form-check-input">
                                                <label class="form-check-label" for="customRadioCliente2">Card</label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                           <!--  <h5 class="card-header bg-primary text-white">App Força de Vendas</h5>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        {!!Form::select('app_valor_aprazo', 'Habilitar valor a prazo', ['0' => 'Não', '1' => 'Sim'])
                                        ->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>
                                </div>
                            </div> -->

                            <h5 class="card-header bg-primary text-white">Responsável Técnico</h5>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        {!!Form::text('resp_tec_nome', 'Nome')
                                        !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!!Form::tel('resp_tec_telefone', 'Telefone')
                                        ->attrs(['class' => 'fone'])
                                        !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!!Form::tel('resp_tec_cpf_cnpj', 'CNPJ')
                                        ->attrs(['class' => 'cpf_cnpj'])
                                        !!}
                                    </div>

                                    <div class="col-md-3">
                                        {!!Form::tel('resp_tec_email', 'Email')
                                        !!}
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">App PDV</h5>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label>Token</label>
                                        <button type="button" class="btn btn-link btn-tooltip btn-sm" data-toggle="tooltip" data-placement="top" title="Esse Token é inserido para conectar o App com este servidor"><i class="ri-file-info-fill"></i></button>
                                        <div class="input-group">
                                            <input readonly type="text" class="form-control tooltipp" id="api_token" name="api_token" value="{{ isset($config) ? $config->api_token : '' }}">
                                            <button type="button" class="btn btn-info" id="btn_token"><a class="ri-refresh-line text-white"></a></button>
                                        </div>
                                        @if($errors->has('api_token'))
                                        <label class="text-danger">Campo Obrigatório</label>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">Pagina Inicial</h5>
                            <div class="card-body">
                                <div class="row">
                                    <div class="row mt-2">
                                        <h5>Componentes</h5>
                                        
                                        @foreach(\App\Models\ConfigGeral::componentesHome() as $key => $t)
                                        <div class="col-lg-3 col-6">
                                            <input name="home_componentes[]" value="{{$t}}" type="checkbox" class="form-check-input check-module" style=" width: 25px; height: 25px;" @isset($item) @if($item->home_componentes != null && sizeof($item->home_componentes) > 0 && in_array($t, $item->home_componentes)) checked="true" @endif @endif>
                                            <label class="form-check-label m-1" for="customCheck1">{{$t}}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">Envio de Fatura WhatsApp Link</h5>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="row g-2">
                                        <div class="col-md-2">
                                            {!!Form::select('status_wpp_link', 'Ativo', ['0' => 'Não', '1' => 'Sim'])
                                            ->attrs(['class' => 'form-select'])
                                            !!}
                                        </div>

                                        <div class="col-md-12">
                                            {!!Form::textarea('mensagem_wpp_link', 'Mensagem padrão impressão')
                                            ->attrs(['class' => '', 'rows' => '5'])
                                            !!}
                                        </div>

                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modal">
                                                <i class="ri-compass-2-fill"></i>
                                                Variáveis dinâmicas
                                            </button>
                                        </div>

                                        <div class="col-md-2">
                                            {!!Form::checkbox('enviar_danfe_wpp_link', 'Enviar DANFE')
                                            ->value(1)
                                            ->checked(isset($item) ? $item->enviar_danfe_wpp_link : 0)
                                            !!}
                                        </div>

                                        <div class="col-md-2">
                                            {!!Form::checkbox('enviar_xml_wpp_link', 'Enviar XML')
                                            ->value(1)
                                            ->checked(isset($item) ? $item->enviar_xml_wpp_link : 0)
                                            !!}
                                        </div>

                                        <div class="col-md-2">
                                            {!!Form::checkbox('enviar_pedido_a4_wpp_link', 'Enviar Pedido A4')
                                            ->value(1)
                                            ->checked(isset($item) ? $item->enviar_pedido_a4_wpp_link : 0)
                                            !!}
                                        </div>


                                    </div>
                                </div>
                            </div>

                            <hr class="mt-2">
                            <div class="col-12" style="text-align: right;">
                                <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
                            </div>
                        </div>
                        {!!Form::close()!!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal" tabindex="-1" aria-labelledby="tituloModal" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="tituloModal">Variáveis dinâmicas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Placeholder</th>
                                    <th>Descrição</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>[nome_cliente]</td>
                                    <td>Nome do cliente</td>
                                </tr>

                                <tr>
                                    <td>[chave]</td>
                                    <td>Chave da NFe</td>
                                </tr>

                                <tr>
                                    <td>[numero_nfe]</td>
                                    <td>Número da NFe</td>
                                </tr>

                                <tr>
                                    <td>[numero_pedido]</td>
                                    <td>Número do pedido</td>
                                </tr>

                                <tr>
                                    <td>[valor_total]</td>
                                    <td>Valor da fatura</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>

        </div>
    </div>
</div>

@endsection

@section('js')
<script src="/tinymce/tinymce.min.js"></script>

<script type="text/javascript">

    $(function(){
        setTimeout(() => {
            $(".inp-cliente_id").select2({
                minimumInputLength: 2,
                language: "pt-BR",
                placeholder: "Digite para buscar o cliente",
                allowClear: true,
                ajax: {
                    cache: true,
                    url: path_url + "api/clientes/pesquisa",
                    dataType: "json",
                    data: function (params) {
                        console.clear();
                        var query = {
                            pesquisa: params.term,
                            empresa_id: $("#empresa_id").val(),
                        };

                        return query;
                    },
                    processResults: function (response) {
                        var results = [];

                        $.each(response, function (i, v) {
                            var o = {};
                            o.id = v.id;
                            o.text = "["+v.numero_sequencial+"] " + v.razao_social + " - " + v.cpf_cnpj;
                            o.value = v.id;
                            results.push(o);
                        });
                        return {
                            results: results,
                        };
                    },
                },
            });
        }, 500)
    })

    $('.select2').select2({
        templateResult: formatOption,
        templateSelection: formatOption,
        width: '100%'
    });

    function formatOption (option) {
        if (!option.id) {
            return option.text;
        }
        var img = $(option.element).data('image');
        if (!img) {
            return option.text;
        }
        var $opt = $(
            '<span><img src="' + img + '" class="img-thumbnail me-2" style="width:24px; height:24px; object-fit:cover;"/> ' + option.text + '</span>'
            );
        return $opt;
    }

    $(function(){
        tinymce.init({ selector: 'textarea.tiny', language: 'pt_BR'})
        setTimeout(() => {
            $('.tox-promotion, .tox-statusbar__right-container').addClass('d-none')
        }, 500)
    })

    function alterarParaTodosEstoque(){
        swal({
            title: "Você está certo?",
            text: "Todos os produtos serão alterados!",
            icon: "warning",
            buttons: true,
            buttons: ["Cancelar", "Alterar"],
            dangerMode: true,
        }).then((isConfirm) => {
            if (isConfirm) {
                let gerenciar_estoque = $('#inp-gerenciar_estoque').val()
                let empresa_id = $('#empresa_id').val()

                $.post(path_url + 'api/produtos/alterar-gerencia-estoque', {
                    gerenciar_estoque: gerenciar_estoque,
                    empresa_id: $('#empresa_id').val(),
                }).done((res) => {
                    console.log(res)
                    swal("Sucesso", "Produtos alterados", "success")

                }).fail((err) => {
                    console.log(err)
                    swal("Erro", err.responseJSON, "error")
                })
            } else {
                swal("", "Nada foi alterado!", "info");
            }
        });
    }

    $('#btn_token').click(() => {

        let token = generate_token(25);
        swal({
            title: "Atenção"
            , text: "Esse token é o responsavel pela comunicação com a API, tenha atenção!!"
            , icon: "warning"
            , buttons: true
            , dangerMode: true
        }).then((confirmed) => {
            if (confirmed) {
                $('#api_token').val(token)
            }
        });
    })

    function generate_token(length) {
        var a = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890".split("");
        var b = [];
        for (var i = 0; i < length; i++) {
            var j = (Math.random() * (a.length - 1)).toFixed(0);
            b[i] = a[j];
        }
        return b.join("");
    }

</script>
@endsection
