@extends('layouts.app', ['title' => 'Sincronizar Produtos Woocommerce'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Sincronizar Produtos Woocommerce</h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('promocao-produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->post()
        ->route('woocommerce-produtos.sincronizar')
        !!}
        <div class="pl-lg-4">


            <div class="card ">
                <div class="row mt-4 m-2">
                    <p class="text-danger"><i class="ri-alert-line"></i>Remova a seleção dos itens que não deseja sincronizar</p>
                    <h5>Produtos</h5>
                    <div class="form-check m-1 form-checkbox-success col-12">
                        <input type="checkbox" checked class="form-check-input" id="check-all">
                        <label class="form-check-label">Selecionar todos <strong>(<span class="contador-selecionados">0</span>)</strong></label>
                    </div>
                    @foreach($produtos as $p)

                    <div class="col-md-3 produtos-check">

                        <div class="form-check">
                            <input type="checkbox" checked name="produto_check[]" class="form-check-input prod-check" value="{{ $p->id }}" id="{{ $p->id }}">
                            <label class="form-check-label" for="{{ $p->id }}">{{ $p->nome }}</label>
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="col-12" style="text-align: right;">
            <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
        </div>
        {!!Form::close()!!}
    </div>
</div>

@endsection

@section('js')
<script type="text/javascript">
    $(function(){ 
        contaSelecionados()
    })


    $(document).on("click", "#check-all", function () {
        if($(this).is(':checked')){
            $('.prod-check').prop('checked', 1)
        }else{
            $('.prod-check').prop('checked', 0)
        }

        setTimeout(() => {
            contaSelecionados()
        }, 100)
    })

    $('.prod-check').click(() => {
        contaSelecionados()
    })

    function contaSelecionados(){
        let total = $(".prod-check:checked").length;
        $(".contador-selecionados").text(total);
    }

    $('#btn-store').click(() => {
        $body = $("body");
        $body.addClass("loading");
    })
</script>
@endsection

