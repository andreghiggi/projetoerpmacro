@extends('layouts.app', ['title' => 'Atribuir Plano'])
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Atribuir Plano <strong class="text-primary">{{ $item->nome }}</strong></h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('home') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        {!!Form::open()->fill($item)
        ->put()
        ->route('contador-empresa.set-plano', [$item->id])
        !!}
        <div class="pl-lg-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <label>Plano</label>
                    <select required id="plano" name="plano_id" class="form-select select2">
                        <option value="">Selecione</option>
                        @foreach($planos as $p)
                        <option value="{{ $p->id }}" data-valor="{{ $p->valor }}">{{ $p->nome }} R$ {{ __moeda($p->valor)}}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 mt-2">
                    {!!Form::tel('valor', 'Valor')
                    ->required()
                    ->attrs(['class' => 'moeda'])
                    !!}
                </div>

                <div class="col-12" style="text-align: right;">
                    <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection

@section('js')
<script type="text/javascript">
    

    $(document).on("change", "#plano", function () {
        if($(this).val()){
            let valor = $('#plano option:selected').data('valor')
            $('#inp-valor').val(convertFloatToMoeda(valor))
        }else{
            $('#inp-valor').val(convertFloatToMoeda(0))
        }
    });

</script>
@endsection
