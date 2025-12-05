@extends('layouts.app', ['title' => 'Planos'])
@section('css')
<style type="text/css">
    .card-img-top{
        height: 240px;
        border-radius: 10px;
    }
</style>
@endsection

@section('content')
<div class="card m-1">
    <div class="row m-3">
        @foreach($planos as $item)
        <div class="col-lg-3 col-12">
            <div class="card">
                <div class="m-1">
                    <img class="card-img-top" src="{{ $item->img }}" alt="Card image cap">
                </div>
                <div class="card-body" style="height: 12rem;">
                    <h4>{{ $item->nome }}</h4>

                    <button class="btn btn-primary w-100" onclick="verDescricao('{{ $item->id }}')">
                        <i class="ri-draft-line"></i> Ver descrição
                    </button>
                    <br>
                    <br>
                    @if($item->valor_implantacao > 0)
                    <h6>Valor de implantação <strong class="text-dark">R$ {{ __moeda($item->valor_implantacao) }}</strong></h6>

                    @endif

                    <h5>Valor total<strong class="text-primary"> R$ {{ __moeda($item->valor) }}</strong></h5>
                    <h6 class="text-success">{{ $item->intervalo_dias }} dias</h6>

                </div>
                <div class="card-footer">
                    @if($config->banco_plano == 'mercado_pago')
                    <button onclick="selectPlano('{{$item->id}}', '{{$item->valor}}', '{{$item->nome}}')" class="btn btn-success btn-pay w-100" data-bs-toggle="modal" data-bs-target="#modal-pay">
                        <i class="ri-shopping-bag-2-fill"></i> Contratar plano
                    </button>
                    @else

                    <a href="{{ route('payment.asaas', [$item->id]) }}" class="btn btn-success btn-pay w-100">
                        <i class="ri-shopping-bag-2-fill"></i> Contratar plano
                    </a>

                    @endif
                </div>

            </div>
        </div>
        @endforeach

    </div>
</div>

<div class="modal fade" id="modal-descricao" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Descrição do plano <strong class="plano_nome"></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@if($config->banco_plano == 'mercado_pago')
<div class="modal fade" id="modal-pay" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <form class="modal-content" method="post" action="{{ route('payment.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Contratar plano <strong class="plano_nome"></strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <p class="text-danger">* Preencha o formulário abaixo com seus dados para gerar o QrCode.</p>
                    <input type="hidden" name="plano_id" id="plano_id">
                    <input type="hidden" name="plano_valor" id="plano_valor">

                    <div class="col-lg-3 col-6">
                        {!!Form::text('nome', 'Nome')
                        ->required()
                        !!}
                    </div>
                    <div class="col-lg-3 col-6">
                        {!!Form::text('sobre_nome', 'Sobre Nome')
                        ->required()
                        !!}
                    </div>

                    <div class="col-lg-4 col-6">
                        {!!Form::text('email', 'Email')
                        ->required()
                        !!}
                    </div>

                    <div class="col-lg-2 col-6">
                        {!!Form::select('docType', 'Tipo documento')
                        ->required()
                        ->id('docType')
                        ->attrs(['data-checkout' => 'docType', 'class' => 'form-select'])
                        !!}
                    </div>

                    <div class="col-lg-3 col-6 mt-2">
                        {!!Form::tel('docNumber', 'Número documento')
                        ->required()
                        ->attrs(['class' => 'cpf_cnpj'])
                        !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-success btn-gerar">Gerar QrCode</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('js')
<script src="https://secure.mlstatic.com/sdk/javascript/v1/mercadopago.js"></script>
<script type="text/javascript">

    $(function(){
        window.Mercadopago.setPublishableKey('{{ $config->mercadopago_public_key }}');
        setTimeout(() => {
            window.Mercadopago.getIdentificationTypes();
        }, 100)
    })
    function selectPlano(id, valor, nome){

        $('.plano_nome').text(nome + " R$ " + convertFloatToMoeda(valor))
        $('#plano_id').val(id)
        $('#plano_valor').val(valor)

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
    }

    function verDescricao(id){

        $.get(path_url + 'api/planos/findOne/'+id)
        .done((res) => {
            // console.log(res)
            $('#modal-descricao').modal('show')
            $('#modal-descricao .modal-body').html(res.descricao)
            $('#modal-descricao .plano_nome').html(res.nome)

        })
        .fail((err) => {
            console.log(err)
            swal("Erro", "Algo deu errado", "error")
        })
    }

    $('.btn-gerar').click(() => {
        $body.addClass("loading");
    })

</script>

@endsection
