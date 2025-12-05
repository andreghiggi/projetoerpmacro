<div class="row g-2">
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
    
    <div class="col-md-4">
        {!!Form::select('cliente_id', 'Cliente')->attrs(['class' => 'select2'])->options(isset($item) ? [$item->cliente_id => $item->cliente->razao_social] : [])
        ->required()
        !!}
    </div>
    <div class="col-md-2">
        <label class="">Início</label>
        <input required type="text" name="data_inicio" id="datetime-datepicker" class="form-control" value="{{ isset($item) ? $item->data_inicio : '' }}">
        @if($errors->has('data_inicio'))
        <label class="text-danger">Campo Obrigatório</label>
        @endif
    </div>
    
    <div class="col-md-3">
        {!!Form::select('funcionario_id', 'Funcionário', ['' => 'Selecione'] + $funcionario->pluck('nome', 'id')->all())->attrs(['class' => 'form-select'])->required(__isSegmentoPlanoOtica())
        !!}
    </div>

    @if(!__isSegmentoPlanoOtica() && $configGeral->tipo_ordem_servico == 'oficina')
    <div class="col-md-3">
        <label>Veículo</label>
        <div class="input-group flex-nowrap">
            <select name="veiculo_id" id="veiculo_id" class="form-select select2">
                <option value="">Selecione</option>
                @foreach($veiculos as $v)
                <option value="{{ $v->id }}">{{ $v->info }}</option>
                @endforeach
            </select>
            @can('veiculos_create')
            <button class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#modal_novo_veiculo" type="button">
                <i class="ri-add-circle-fill"></i>
            </button>
            @endcan
        </div>
    </div>
    @endif

    @if($configGeral->tipo_ordem_servico == 'assistencia técinica')
    <hr>
    <div class="col-md-2">
        {!!Form::select('tipo_servico', 'Tipo do serviço', \App\Models\OrdemServico::tiposDeServico())
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-3">
        {!!Form::text('equipamento', 'Equipamento')->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('numero_serie', 'Número de série')
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('cor', 'Cor')
        !!}
    </div>

    <div class="col-md-12">
        {!!Form::textarea('diagnostico_cliente', 'Diagnóstico do cliente')
        ->attrs(['rows' => '4', 'class' => 'tiny'])
        !!}
    </div>

    @endif

    <div class="col-md-12">
        {!!Form::textarea('descricao', 'Descrição/Observação')
        ->attrs(['rows' => '6', 'class' => 'tiny'])
        !!}
    </div>

    @if(__isSegmentoPlanoOtica())
    @include('ordem_servico.partials.otica_forms')
    @endif


    <hr class="mt-4">
    <div class="col-12" style="text-align: right;">
        <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
    </div>
</div>

@section('js')
<script src="/tinymce/tinymce.min.js"></script>
<script type="text/javascript">
    $(function(){
        tinymce.init({ selector: 'textarea.tiny', language: 'pt_BR'})

        setTimeout(() => {
            $('.tox-promotion, .tox-statusbar__right-container').addClass('d-none')
        }, 500)
    })

    $(document).on("click", ".btn-store-veiculo", function () {
        var json = {};
        var a = $("#modal_novo_veiculo").serializeArray();
        let msg = ""
        $("#modal_novo_veiculo").find('input, select').each(function () {
            if (($(this).val() == "" || $(this).val() == null) && $(this).attr("type") != "hidden" && $(this).attr("type") != "file" && !$(this).hasClass("ignore")) {
                if($(this).prev()[0].textContent){
                    msg += "Informe o campo " + $(this).prev()[0].textContent + "\n"
                }
            }
            if($(this)[0].name){
                let name = $(this)[0].name
                name = name.replace("novo_", "")
                json[name] = $(this).val()
            }
        })

        json['empresa_id'] = $('#empresa_id').val()
        // console.log(json)
        // return
        setTimeout(() => {
            if(msg == ""){
            // console.log(json)
            $.post(path_url + "api/veiculos/store", json)
            .done((res) => {
                $('#modal_novo_veiculo .btn-close').trigger('click')

                console.log(res)
                swal("Sucesso", "Veículo cadastrado!", "success")

                var newOption = new Option(res.info, res.id, false, false);
                $('#veiculo_id').append(newOption);

                $("#modal_novo_veiculo").find('input, select').each(function () {
                    $(this).val('')
                })

            })
            .fail((err) => {
                console.log(err)
                swal("Erro", "Erro ao cadastrar veículo: " + err.responseJSON, "error")
                .then(() => {
                    $('#modal_novo_veiculo .btn-close').trigger('click')

                })
            })
        }else{
            swal("Alerta", msg, "warning")
        }
    }, 300)
    })
</script>

@endsection
