@extends('layouts.app', ['title' => 'Pagamento'])
@section('css')
<style type="text/css">
    .input-group-text{
        height: 40px;
    }
</style>
@endsection

@section('content')
<div class="card">

    @if(isset($dataAsaas['encodedImage']) && $config->banco_plano == 'asaas')
    <div class="row mt-5 m-2">
        <h3 class="text-center text-muted">{{ $plano->plano->nome }}</h3>
        <h5 class="text-center text-primary">Valor do plano: R${{ __moeda($plano->valor) }}</h5>
        <p class="text-center text-primary">Escaneie ou copie o código para efetuar o pagamento do plano, permaneça nesta tela até finalizar o processo!</p>
        <p class="text-center text-danger">Após o pagamento aguarde nessa tela até ser redirecionado!</p>

        <div class="col-lg-4 offset-lg-4 text-center">
            <img style="width: 400px; height: 400px;" src="data:image/jpeg;base64,{{ $dataAsaas['encodedImage'] }}"/>
        </div>
        <div class="input-group">
            <input type="text" class="form-control" value="{{ $dataAsaas['payload'] }}" id="qrcode_input" />

            <div class="input-group-append" onclick="copy()">
                <span class="input-group-text">
                    <i class="ri-file-copy-line">
                    </i>
                </span>
            </div>
        </div>
    </div>
    @endif

    @if($config->banco_plano == 'mercado_pago')
    <div class="card-body">
        <form method="post" action="{{ route('payment.store') }}">
            @csrf
            <div class="row">
                <p class="text-danger">* Preencha o formulário abaixo com seus dados para gerar o QrCode.</p>

                <input type="hidden" name="plano_id" value="{{ $plano->plano_id }}">
                <input type="hidden" name="plano_valor" value="{{ $plano->valor }}">
                <div class="col-md-2 col-6">
                    {!!Form::text('nome', 'Nome')
                    ->required()
                    !!}
                </div>
                <div class="col-md-2 col-6">
                    {!!Form::text('sobre_nome', 'Sobre Nome')
                    ->required()
                    !!}
                </div>

                <div class="col-md-3 col-6">
                    {!!Form::text('email', 'Email')
                    ->required()
                    !!}
                </div>

                <div class="col-md-2 col-6">
                    {!!Form::select('docType', 'Tipo documento')
                    ->required()
                    ->id('docType')
                    ->attrs(['data-checkout' => 'docType', 'class' => 'form-select'])
                    !!}
                </div>

                <div class="col-md-2 col-6">
                    {!!Form::tel('docNumber', 'Número documento')
                    ->required()
                    ->attrs(['class' => 'cpf_cnpj'])
                    !!}
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-2 col-6">
                    <button type="submit" class="btn btn-success btn-gerar"><i class="ri-qr-code-fill"></i> Gerar QrCode</button>
                </div>
            </div>
        </form>
    </div>
    @endif

</div>

@endsection

<!-- js asaas -->
@isset($dataAsaas['encodedImage'])
@section('js')
<script type="text/javascript">
    var myInterval;
    function copy(){
        const inputTest = document.querySelector("#qrcode_input");

        inputTest.select();
        document.execCommand('copy');

        swal("", "Código pix copiado!!", "success")
    }

    myInterval = setInterval(() => {
        console.clear()
        let json = {
            id: '{{ $dataAsaas["id"] }}',
            plano_id: '{{ $plano->id }}',
            empresa_id: $('#empresa_id').val()
        }
        console.log(json)
        $.get(path_url+'api/payment-status-asaas', json)
        .done((success) => {
            console.log(success)
            if(success == "pago"){
                clearInterval(myInterval)
                swal("Sucesso", "Pagamento aprovado", "success").then(() => {
                    location.href = path_url
                })
            }
        })
        .fail((err) => {
            console.log(err)
        })
    }, 3000)



</script>

@endsection
@endif

<!-- js mercado pago -->
@if($config->banco_plano == 'mercado_pago')
@section('js')
<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
<script type="text/javascript">

    $(function(){
        window.Mercadopago.setPublishableKey('{{ $config->mercadopago_public_key }}');
        setTimeout(() => {
            window.Mercadopago.getIdentificationTypes();
        }, 100)

        setTimeout(() => {
            let empresa_id = $('#empresa_id').val()
            $.get(path_url + 'api/empresas/find', {empresa_id: empresa_id})
            .done((res) => {

                let cpf_cnpj = res.cpf_cnpj.replace(/[^0-9]/g,'')

                let n = res.nome.split(' ')
                $('#inp-docNumber').val(cpf_cnpj)
                $('#inp-nome').val(n[0])
                $('#inp-sobre_nome').val(n[1])
                $('#inp-email').val(res.email)

                if(cpf_cnpj.length == 14){
                    $('#docType').val('CNPJ').change()
                }

            })
            .fail((err) => {
                console.log(err)
                swal("Erro", "Algo deu errado", "error")
            })
        }, 1000)
    })
    function selectPlano(id, valor, nome){

        $('.plano_nome').text(nome + " R$ " + convertFloatToMoeda(valor))
        $('#plano_id').val(id)
        $('#plano_valor').val(valor)
    }

    $('.btn-gerar').click(() => {
        // $body.addClass("loading");
    })

</script>

@endsection
@endif
