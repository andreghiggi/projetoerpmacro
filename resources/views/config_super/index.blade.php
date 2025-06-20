@extends('layouts.app', ['title' => 'Configurações'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Configuração Super</h4>
                <hr>
                <div class="row mt-3">
                    <h5>Responsável Técnico</h5>
                    <div class="col-lg-12">
                        {!!Form::open()->fill($item)
                        ->post()
                        ->route('configuracao-super.store')
                        ->multipart()
                        !!}
                        
                        <div class="row mt-3 g-2">
                            <div class="col-md-4">
                                {!!Form::text('name', 'Nome')
                                ->required()
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('cpf_cnpj', 'CNPJ')
                                ->required()
                                ->attrs(['class' => 'cpf_cnpj'])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('telefone', 'Telefone')
                                ->attrs(['class' => 'fone'])
                                ->required()
                                !!}
                            </div>
                            <div class="col-md-4">
                                {!!Form::text('email', 'Email')
                                ->required()
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::select('usar_resp_tecnico', 'Usar Resp. Técnico', [0 => 'Não', 1 => 'Sim'])
                                ->required()
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            <div class="col-md-2">
                                {!!Form::select('auto_cadastro', 'Permitir auto cadastro', [0 => 'Não', 1 => 'Sim'])
                                ->required()
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                            <hr class="mt-4">
                            <h5 class="text-success">Recebimento dos planos (PIX)</h5>

                            <div class="col-md-2">
                                {!!Form::select('banco_plano', 'Conta para recebimento', ['mercado_pago' => 'Mercado pago', 'asaas' => 'Asaas'])
                                ->required()
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>
                            <div class="col-md-10"></div>

                            <div class="col-md-12">
                                {!!Form::text('asaas_token', 'Asaas Token')
                                !!}
                            </div>

                            <div class="col-md-6">
                                {!!Form::text('mercadopago_public_key', 'Mercado Pago Public Key')
                                !!}
                            </div>
                            <div class="col-md-6">
                                {!!Form::text('mercadopago_access_token', 'Mercado Pago Access Token')
                                !!}
                            </div>

                            <hr class="mt-4">
                            <h5 class="text-success">Recebimento dos planos Asaas (Boleto)</h5>

                            <div class="col-md-12">
                                {!!Form::text('asaas_token_boleto', 'Asaas Token')
                                !!}
                            </div>

                            <div class="col-md-2">
                                {!!Form::select('receber_com_boleto', 'Receber com boleto', [0 => 'Não', 1 => 'Sim'])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            <div class="col-md-2">
                                {!!Form::tel('percentual_juros_padrao_boleto', '% Juros padrão')
                                ->attrs(['class' => 'percentual'])
                                !!}
                            </div>

                            <div class="col-md-2">
                                {!!Form::tel('percentual_multa_padrao_boleto', '% Multa padrão')
                                ->attrs(['class' => 'percentual'])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('dias_atraso_suspender_boleto', 'Dias de atraso suspensão')
                                ->attrs(['data-mask' => '00'])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::select('sandbox_boleto', 'Ambiente', [1 => 'Sandbox', 0 => 'Produção'])
                                ->attrs(['class' => 'form-select'])
                                !!}
                            </div>

                            <hr class="mt-4">
                            <div class="col-md-6">
                                <h5 class="text-success">Ativar conta do cliente MarketPlace</h5>

                                {!!Form::text('sms_key', 'SMS key comtele')
                                !!}
                            </div>
                            <div class="col-md-6">
                                <h5 class="text-success">Para enviar mensagens de alteração do pedido delivery https://criarwhats.com</h5>

                                {!!Form::text('token_whatsapp', 'Token WhatsApp')
                                !!}
                            </div>

                            <hr class="mt-4">

                            <h5 class="text-success">Cálculo dos correios</h5>
                            <div class="col-md-3">
                                {!!Form::text('usuario_correios', 'Usuário')
                                !!}
                            </div>
                            <div class="col-md-5">
                                {!!Form::text('codigo_acesso_correios', 'Código de accesso')
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::text('cartao_postagem_correios', 'Cartão postagem')
                                !!}
                            </div>

                            <hr class="mt-4">

                            <h5 class="text-success">NFSe</h5>
                            <div class="col-md-10">
                                {!!Form::text('token_auth_nfse', 'Token integra notas')
                                !!}
                            </div>

                            <hr class="mt-4">

                            <h5 class="text-success">Emissão de documentos</h5>
                            <div class="col-md-2">
                                {!!Form::tel('timeout_nfe', 'Tempo de espera NFe')
                                ->attrs(['data-mask' => '00'])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('timeout_nfce', 'Tempo de espera NFCe')
                                ->attrs(['data-mask' => '00'])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('timeout_cte', 'Tempo de espera CTe')
                                ->attrs(['data-mask' => '00'])
                                !!}
                            </div>
                            <div class="col-md-2">
                                {!!Form::tel('timeout_mdfe', 'Tempo de espera MDFe')
                                ->attrs(['data-mask' => '00'])
                                !!}
                            </div>

                            <hr class="mt-4">
                            <h5 class="text-success">Api Rest</h5>

                            <div class="col-md-4">
                                <label for="" class="required">Token</label>
                                <div class="input-group">
                                    <input readonly required type="text" class="form-control" id="api_token" name="token_api" value="{{ isset($item) ? $item->token_api : '' }}">
                                    <button type="button" class="btn btn-info" id="btn_token"><a class="ri-refresh-line text-white"></a></button>
                                </div>
                            </div>

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
<script type="text/javascript">
    $('#btn_token').click(() => {

        let token = generate_token(25);
        swal({
            title: "Atenção", 
            text: "Esse token é o responsavel pela comunicação com a API!!", 
            icon: "warning", 
            buttons: true,
            dangerMode: true
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
