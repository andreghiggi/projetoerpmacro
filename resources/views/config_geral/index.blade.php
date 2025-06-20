@extends('layouts.app', ['title' => 'Configurações Gerais'])
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
                                    <div class="col-md-2">
                                        {!!Form::text('senha_manipula_valor', 'Senha para desconto/acréscimo')                                        
                                        !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!!Form::select('agrupar_itens', 'Agrupar itens', ['0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select'])
                                        !!}
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('tipo_comissao', 'Tipo de comissão', ['percentual_vendedor' => '% Vendedor', 'percentual_margem' => '% Margem'])->attrs(['class' => 'form-select tooltipp'])
                                        !!}
                                        <div class="text-tooltip d-none">
                                            Marcar como sim se for usar esta categoria no cardápio
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        {!!Form::select('modelo', 'Modelo', ['light' => 'Light', 'compact' => 'Compact'])->attrs(['class' => 'form-select'])
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

                                    <div class="col-md-12">
                                        {!!Form::textarea('mensagem_padrao_impressao_venda', 'Mensagem padrão impressão')->attrs(['class' => 'tiny', 'rows' => '5'])
                                        !!}
                                    </div>
                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">Ordem de Serviço</h5>
                            <div class="card-body">
                                <div class="row">
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
                                        <div class="input-group input-group-merge" style="margin-top: -8px">
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
                                        ->attrs(['class' => 'cliente_id'])
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

                                </div>
                            </div>

                            <h5 class="card-header bg-primary text-white">App Força de Vendas</h5>
                            <div class="card-body">
                                <div class="row g-2">
                                    <div class="col-md-2">
                                        {!!Form::select('app_valor_aprazo', 'Habilitar valor a prazo', ['0' => 'Não', '1' => 'Sim'])
                                        ->attrs(['class' => 'form-select'])
                                        !!}
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
@endsection

@section('js')
<script src="/tinymce/tinymce.min.js"></script>

<script type="text/javascript">

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
</script>
@endsection
