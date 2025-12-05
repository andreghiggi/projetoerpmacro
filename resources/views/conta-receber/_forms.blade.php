@section('css')
<style type="text/css">
    input[type="file"] {
        display: none;
    }

    .file-certificado label {
        padding: 8px 8px;
        width: 100%;
        background-color: #8833FF;
        color: #FFF;
        text-transform: uppercase;
        text-align: center;
        display: block;
        margin-top: 20px;
        cursor: pointer;
        border-radius: 5px;
    }

    .card-body strong{
        color: #8833FF;
    }

</style>
@endsection
<div class="row g-2">

    @if(isset($diferenca) && $diferenca > 0)
    <div class="col-md-12 mb-3">
        <p class="text-info">Crie uma nova conta à receber ou somente finalize abaixo!</p>
        <a href="{{ route('conta-receber.index') }}" class="btn btn-dark btn-sm px-3">
            <i class="ri-arrow-left-double-fill"></i>Finalizar novo recebimento
        </a>
    </div>
    @endif

    @if(__countLocalAtivo() > 1)
    <div class="col-md-2">
        <label for="">Local</label>

        <select id="inp-local_id" required class="select2 class-required" data-toggle="select2" name="local_id">
            <option value="">Selecione</option>
            @foreach(__getLocaisAtivoUsuario() as $local)
            <option @isset($item) @if($item->local_id == $local->id) selected @endif @endif value="{{ $local->id }}">{{ $local->descricao }}</option>
            @endforeach
        </select>
    </div>
    @else
    <input id="inp-local_id" type="hidden" value="{{ __getLocalAtivo() ? __getLocalAtivo()->id : '' }}" name="local_id">
    @endif

    <div class="col-md-3">
        {!!Form::text('descricao', 'Descrição')
        !!}
    </div>
    <div class="col-md-5">
        <label>Cliente</label>
        <div class="input-group flex-nowrap">
            <select id="inp-cliente_id" name="cliente_id" class="cliente_id">
                @if(isset($item) && $item->cliente)
                <option value="{{ $item->cliente_id }}">{{ $item->cliente->razao_social }}</option>
                @endif
            </select>
            @can('clientes_create')
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modal_novo_cliente" type="button">
                <i class="ri-add-circle-fill"></i>
            </button>
            @endcan
        </div>
    </div>
    <div class="col-md-2">
        {!!Form::text('valor_integral', 'Valor Integral')->attrs(['class' => 'moeda'])->value(isset($item) ? __moeda($item->valor_integral) : '')->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::date('data_vencimento', 'Data Vencimento')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Conta Recebida', ['0' => 'Não', '1' => 'Sim'])->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('tipo_pagamento', 'Tipo Pagamento', App\Models\ContaReceber::tiposPagamento())->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::select('categoria_conta_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
        ->attrs(['class' => 'form-select'])
        !!}
    </div>

    <div class="col-md-3 file-certificado">
        {!! Form::file('file', 'Procurar arquivo')
        ->attrs(['accept' => '.pdf, image/*']) !!}
        <span class="text-danger" id="filename"></span>
    </div>

    <div class="col-md-2">
        {!!Form::text('referencia', 'Nº documento')
        !!}
    </div>

    <div class="col-md-4">
        {!!Form::text('observacao', 'Observação')
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::text('observacao2', 'Observação 2')
        !!}
    </div>
    <div class="col-md-4">
        {!!Form::text('observacao3', 'Observação 3')
        !!}
    </div>

    @if(isset($item) && $item->arquivo != null)
    <a href="{{ route('conta-receber.download-file', [$item->id]) }}">
        <i class="ri-file-download-line"></i>
        Baixar arquivo
    </a>
    @endif

    <hr class="mt-4">

    @if(!isset($item))
    <p class="text-danger">
        * Campo abaixo deve ser preenchido se ouver recorrência para este registro
    </p>

    <div class="col-md-2">
        {!!Form::tel('recorrencia', 'Data')
        ->attrs(['data-mask' => '00/00'])
        ->placeholder('mm/aa')
        !!}
    </div>
    @endif

    <div class="row tbl-recorrencia d-none mt-2">
    </div>

    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script src="/js/novo_cliente.js"></script>
<script>
    $('#inp-recorrencia').blur(() => {
        let data = $('#inp-recorrencia').val()
        if (data.length == 5) {
            let vencimento = $('#inp-data_vencimento').val()
            let valor = $('#inp-valor_integral').val()
            if (valor && vencimento) {
                let item = {
                    data: data, 
                    vencimento: vencimento,
                    valor: valor
                }
                $.get(path_url + 'api/conta-receber/recorrencia', item)
                .done((html) => {
                    $('.tbl-recorrencia').html(html)
                    $('.tbl-recorrencia').removeClass('d-none')

                }).fail((err) => {
                    console.log(err)

                })
            } else {
                swal("Algo saiu errado", "Informe o valor e vencimento data conta base!", "warning")
            }
        } else {
            swal("Algo saiu errado", "Informe uma data válida mm/aa exemplo 12/25", "warning")
        }
    })

</script>
@endsection
