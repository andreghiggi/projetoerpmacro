<div class="row g-2">
    <div class="col-md-3">
        @isset($funcionarios)
        {!!Form::select('funcionario_id', 'Funcionário', ['' => 'Selecione'] + $funcionarios->pluck('nome', 'id')->all())->attrs(['class' => 'form-select'])->required()
        !!}
        @else
        <input type="hidden" value="{{ $item->id }}" name="funcionario_id">
        <h4>Funconário <strong class="text-success">{{ $item->nome }}</strong></h4>
        @endif
    </div>

    <hr class="mt-4">
    <div class="col-md-2">
        {!!Form::select('dia', 'Dia', App\Models\Interrupcoes::getDias())->attrs(['class' => 'form-select'])->required()
        !!}
    </div>
    <div class="col-md-2">
        {!!Form::text('inicio', 'Início')->attrs(['class' => 'timer'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::text('fim', 'Fim')->attrs(['class' => 'timer'])->required()
        !!}
    </div>

    <div class="col-md-2">
        {!!Form::select('status', 'Ativo', ['1' => 'Sim', '0' => 'Não'])
        ->attrs(['class' => 'form-select'])->required()
        !!}
    </div>

    <div class="col-md-4">
        <label class="form-label required">Motivo</label>
        <div class="input-group" style="margin-top: -8px">
            <select required type="text" name="motivo" id="motivo" class="form-select">
                <option value="">Selecione</option>
                @foreach($motivos as $m)
                <option @isset($item) @if($item->motivo == $m->motivo) selected @endif @endif value="{{ $m->motivo }}">{{ $m->motivo }}</option>
                @endforeach
            </select>
            <div class="input-group-text bg-danger" onclick="novoMotivo()">
                <span class="ri-menu-add-line text-light"></span>
            </div>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn-success px-5">Salvar</button>
    </div>
</div>
@include('modals._novo_motivo', ['not_submit' => true])

@section('js')
<script type="text/javascript">

    setTimeout(() => {
        // var newOption = new Option('2323', 1, false, 1);
        // $('#inp-motivo').append(newOption).trigger('change');

    }, 10)

    function novoMotivo(){
        $('#modal-novo-motivo').modal('show')
    }

    $('.btn-salvar-motivo').click(() => {
        let motivo = $('#novo_motivo').val()

        if(motivo.length >= 4){
            let empresa_id = $("#empresa_id").val();

            $.post(path_url + "api/interrupcao/store-motivo", {
                motivo: motivo,
                empresa_id: empresa_id
            })
            .done((success) => {
                $('#novo_motivo').val('')
                console.log(success)
                var newOption = new Option(motivo, motivo, false, 1);
                $('#motivo').append(newOption).trigger('change');
                swal("Sucesso", "Motivo cadastrado!", "success")
                $('#modal-novo-motivo').modal('hide')

            })
            .fail((err) => {
                console.log(err)
                swal("Erro", "Algo deu errado", "error")
                $('#modal-novo-motivo').modal('hide')

            })
        }else{
            swal("Alerta", "Informe no mínimo 4 caracteres", "warning")
        }
    })
    
</script>
@endsection
