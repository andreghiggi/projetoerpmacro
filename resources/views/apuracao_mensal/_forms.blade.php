<div class="row">
    <p class="text-info">Selecione o funcionário para buscar os eventos de pagamento!</p>

    <div class="col-6 mt-1">
        <label class="col-form-label" id="">Funcionário</label>
        <div class="input-group">
            @isset($item)
            <h4>{{$item->nome}}</h4>
            @else
            <select class="select2" name="funcionario_id" id="funcionario_id">
                <option value="">Selecione o funcionário</option>
                @foreach($funcionarios as $f)
                <option value="{{$f->id}}">{{ $f->nome }} ({{ $f->cpf_cnpj }})</option>
                @endforeach
            </select>
            @endif
        </div>
    </div>
    <div class="col-md-2 mt-3">
        <br>
        <select class="form-select" name="mes">
            @foreach(\App\Models\ApuracaoMensal::mesesApuracao() as $key => $m)
            <option value="{{$m}}" @if($key==$mesAtual) selected @endif>{{ ($m) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-2 mt-3">
        <br>
        <select class="form-select" name="ano">
            @foreach(\App\Models\ApuracaoMensal::anosApuracao() as $key => $a)
            <option value="{{$a}}">{{ $a }}</option>
            @endforeach
        </select>
    </div>

    <div class="row mt-4">
        <div class="table-responsive">
            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th></th>
                        <th>Evento</th>
                        <th>Condição</th>
                        <th>Valor</th>
                        <th>Método</th>
                    </tr>
                </thead>
                <tbody id="body" class="datatable-body">
                    <tr>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="row mt-4">

        <div class="col-md-2 col-12">
            {!!Form::select('tipo_pagamento', 'Tipo de Pagamento', ['' => 'Selecione'] + App\Models\ApuracaoMensal::tiposPagamento())->attrs(['class' => 'form-select'])
            ->required()
            !!}
        </div>
        <div class="col-md-2 col-12">
            {!!Form::tel('valor_total', 'Valor total')->attrs(['class' => 'moeda'])->required()
            !!}
        </div>

        <div class="col-md-6 col-12">
            {!!Form::text('observacao', 'Observação')
            !!}
        </div>
    </div>

    <hr class="mt-4">
    <div class="col-12 mt-3 float-end">
        <button disabled type="submit" class="btn btn-success px-5">Salvar</button>
    </div>
</div>


@section('js')
<script type="text/javascript">
    $(function() {
        $('#funcionario_id').val('').change()
    })
    $('#funcionario_id').change(() => {
        $('.datatable-body').html('')
        $('.func-select').addClass('d-none')
        let funcionario = $('#funcionario_id').val()
        if (funcionario) {

            $.get(path_url + 'apuracao-mensal/get-eventos/' + funcionario)
            .done((html) => {
                console.clear();
                console.log(html)
                if (html == "") {
                    swal("Erro", "Funcionário sem eventos de pagamento cadastrados!", "error")
                } else {
                    $('.func-select').removeClass('d-none')
                    $('.datatable-body').html(html)
                    calcTotal()
                }
            }).fail((err) => {
                console.log(err)
            })
        }
    })

    function calcTotal() {
        console.clear()
        let total = 0
        $('.dynamic-form').each(function() {
            console.log($(this))
            var value = $(this).find('input').val();
            var condicao = $(this).find('.condicao_chave').val();
            console.log("condicao", condicao)
            if (value) {
                // value = value.replace(",", ".")
                value = convertMoedaToFloat(value)
                if (condicao == "soma") {
                    total += value
                } else {
                    total -= value
                }
            }
        })
        setTimeout(() => {
            $('#inp-valor_total').val(convertFloatToMoeda(total))
            $('.value').addClass('moeda')
            if(total > 0){
                $('.btn-success').removeAttr('disabled')
            }
        }, 100)
    }
    $(".datatable-body").on('click', '.btn-delete-row', function () {
        $(this).closest('tr').remove();
        swal("Sucesso", "Evento removido!", "success")
        calcTotal()
    });


    $(document).on("blur", ".value", function () {
        calcTotal()
    });
</script>
@endsection