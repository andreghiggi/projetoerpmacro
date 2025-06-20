<hr>
@section('css')
<style type="text/css">
    .btn{
        margin-top: 3px;
}

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
</style>
@endsection
<div class="col-md-12">
        <h4>Receita Ótica</h4>
</div>
<div class="col-md-3">
        {!!Form::select('medico_id', 'Médico')->required()
        ->options(isset($item->oticaOs) && $item->oticaOs->medico ? [$item->oticaOs->medico->id => $item->oticaOs->medico->nome] : [])
        !!}
</div>

<div class="col-md-3">
        {!!Form::select('convenio_id', 'Convênio')
        ->options(isset($item->oticaOs) && $item->oticaOs->convenio != null ? [$item->oticaOs->convenio->id => $item->oticaOs->convenio->nome] : [])
        !!}
</div>

<div class="col-md-2">
        {!!Form::date('validade', 'Validade da Receita')
        ->value(isset($item->oticaOs) ? $item->oticaOs->validade : '')
        !!}
</div>

<div class="col-md-12">
        {!!Form::textarea('observacao_receita', 'Observação da Receita')
        ->attrs(['rows' => '4', 'class' => 'tiny'])
        ->value(isset($item->oticaOs) ? $item->oticaOs->observacao_receita: '')
        !!}
</div>

<div class="col-md-12">

        <table class="table">
                <thead>
                        <tr>
                                <th></th>
                                <th>Esférico</th>
                                <th>Cilíndrico</th>
                                <th>Eixo</th>
                                <th>Altura</th>
                                <th>DNP</th>
                        </tr>
                </thead>
                <tbody>
                        <tr>
                                <td style="width: 200px">
                                        <i class="ri-eye-line"></i> OD LONGE
                                </td>
                                <td>
                                        <input class="form-control" name="esferico_longe_od" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->esferico_longe_od : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="cilindrico_longe_od" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->cilindrico_longe_od : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="eixo_longe_od" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->eixo_longe_od : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="altura_longe_od" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->altura_longe_od : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="dnp_longe_od" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->dnp_longe_od : '' }}">
                                </td>

                        </tr>
                        <tr>
                                <td style="width: 200px">
                                        <i class="ri-eye-line"></i> OE LONGE
                                </td>
                                <td>
                                        <input class="form-control" name="esferico_longe_oe" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->esferico_longe_oe : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="cilindrico_longe_oe" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->cilindrico_longe_oe : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="eixo_longe_oe" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->eixo_longe_oe : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="altura_longe_oe" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->altura_longe_oe : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="dnp_longe_oe" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->dnp_longe_oe : '' }}">
                                </td>
                        </tr>
                        <tr>
                                <td style="width: 200px">
                                        <i class="ri-eye-line"></i> OD PERTO
                                </td>
                                <td>
                                        <input class="form-control" name="esferico_perto_od" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->esferico_perto_od : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="cilindrico_perto_od" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->cilindrico_perto_od : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="eixo_perto_od" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->eixo_perto_od : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="altura_perto_od" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->altura_perto_od : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="dnp_perto_od" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->dnp_perto_od : '' }}">
                                </td>

                        </tr>
                        <tr>
                                <td style="width: 200px">
                                        <i class="ri-eye-line"></i> OE PERTO
                                </td>
                                <td>
                                        <input class="form-control" name="esferico_perto_oe" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->esferico_perto_oe : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="cilindrico_perto_oe" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->cilindrico_perto_oe : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="eixo_perto_oe" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->eixo_perto_oe : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="altura_perto_oe" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->altura_perto_oe : '' }}">
                                </td>
                                <td>
                                        <input class="form-control" name="dnp_perto_oe" value="{{ isset($item) && $item->medicaoReceitaOs ? $item->medicaoReceitaOs->dnp_perto_oe : '' }}">
                                </td>
                        </tr>
                </tbody>
                
        </table>
</div>

<div class="col-md-3 file-certificado">
        {!! Form::file('arquivo', 'Arquivo de receita') !!}
        <span class="text-danger" id="filename"></span>

        @if(isset($item->oticaOs) && $item->oticaOs->arquivo_receita)
        <a href="{{ route('ordem-servico.download-arquivo', [$item->id]) }}">Download arquivo</a>
        @endif
</div>
<hr>
<div class="col-md-12">
        <h4>Informações Adicionais</h4>
</div>

<div class="col-md-3">
        {!!Form::select('laboratorio_id', 'Laboratório')
        ->options(isset($item->oticaOs) && $item->oticaOs->laboratorio != null ? [$item->oticaOs->laboratorio->id => $item->oticaOs->laboratorio->nome] : [])
        !!}
</div>

<div class="card">
        <div class="card-header">
                <h5>Lente</h5>
        </div>
        <div class="card-body">
                <div class="row g-2">

                        <div class="col-md-2">
                                {!!Form::select('tipo_lente', 'Tipo da lente', ['' => 'Selecione', 'Pronta' => 'Pronta', 'Surfaçada' => 'Surfaçada'])
                                ->attrs(['class' => 'form-select'])
                                ->value(isset($item->oticaOs) ? $item->oticaOs->tipo_lente : '')
                                !!}
                        </div>
                        <div class="col-md-2">
                                {!!Form::select('material_lente', 'Material da lente', ['' => 'Selecione', 'Policarbonato' => 'Policarbonato', 'Resina' => 'Resina', 'Trivex' => 'Trivex'])
                                ->attrs(['class' => 'form-select'])
                                ->value(isset($item->oticaOs) ? $item->oticaOs->material_lente : '')
                                !!}
                        </div>
                        <div class="col-md-3">
                                {!!Form::text('descricao_lente', 'Descrição da lente')
                                ->value(isset($item->oticaOs) ? $item->oticaOs->descricao_lente : '')
                                !!}
                        </div>
                        <div class="col-md-2">
                                {!!Form::text('coloracao_lente', 'Coloração da lente')
                                ->value(isset($item->oticaOs) ? $item->oticaOs->coloracao_lente : '')
                                !!}
                        </div>

                        <div class="col-md-3">
                                <label for="">Tratamentos</label>
                                <select class="select2 form-control select2-multiple" name="tratamentos[]" data-toggle="select2" multiple="multiple">
                                        @foreach($tratamentos as $t)
                                        <option @if(in_array($t->id, (isset($item->oticaOs) ? $item->oticaOs->tratamentos : []))) selected @endif value="{{ $t->id }}">{{ $t->nome }}</option>
                                        @endforeach
                                </select>
                        </div>
                </div>
        </div>
</div>

<div class="card">
        <div class="card-header">
                <h5>Armação</h5>
        </div>
        <div class="card-body">
                <div class="row g-2">
                        <div class="col-md-12">
                                <label>Formato da armação</label>
                                <div class="row">
                                        @foreach($formatosArmacao as $f)
                                        
                                        <div class="col-md-3">
                                                <div class="card">
                                                        <div class="card-bady p-2">
                                                                <input class="form-check-input mt-3" type="radio" name="formato_armacao_id" value="{{ $f->id }}" id="radio-{{$f->id}}" @if(isset($item->oticaOs) && $item->oticaOs->formato_armacao_id == $f->id) checked @endif>
                                                                <label class="form-check-label" style="margin-left: 15px;" for="radio-{{$f->id}}">
                                                                        <img class="img-120" src="{{ $f->img }}">
                                                                        <span style="margin-left: 10px;">{{ $f->nome }}</span>
                                                                </label>   
                                                        </div>
                                                </div>
                                        </div>
                                        @endforeach
                                </div>
                        </div>

                        <div class="col-md-2">
                                {!!Form::select('armacao_propria', 'Armação própria', ['0' => 'Não', '1' => 'Sim'])
                                ->attrs(['class' => 'form-select'])
                                ->value(isset($item->oticaOs) ? $item->oticaOs->armacao_propria : '')
                                !!}
                        </div>
                        <div class="col-md-2">
                                {!!Form::select('armacao_segue', 'Segue armação', ['0' => 'Não', '1' => 'Sim'])
                                ->attrs(['class' => 'form-select'])
                                ->value(isset($item->oticaOs) ? $item->oticaOs->armacao_segue : '')
                                !!}
                        </div>
                        <div class="col-md-2">
                                {!!Form::select('tipo_armacao_id', 'Tipo de armação', ['' => 'Selecione'] + $tiposArmacao->pluck('nome', 'id')->all())
                                ->attrs(['class' => 'form-select'])
                                ->value(isset($item->oticaOs) ? $item->oticaOs->tipo_armacao_id : '')
                                !!}
                        </div>

                        <div class="col-md-2">
                                {!!Form::text('armacao_aro', 'Aro')
                                ->value(isset($item->oticaOs) ? $item->oticaOs->armacao_aro : '')
                                !!}
                        </div>
                        <div class="col-md-2">
                                {!!Form::text('armacao_ponte', 'Ponte')
                                ->value(isset($item->oticaOs) ? $item->oticaOs->armacao_ponte : '')
                                !!}
                        </div>
                        <div class="col-md-2">
                                {!!Form::text('armacao_maior_diagonal', 'Maior diagonal')
                                ->value(isset($item->oticaOs) ? $item->oticaOs->armacao_maior_diagonal: '')
                                !!}
                        </div>
                        <div class="col-md-2">
                                {!!Form::text('armacao_altura_vertical', 'Altura vertical')
                                ->value(isset($item->oticaOs) ? $item->oticaOs->armacao_altura_vertical : ' ')
                                !!}
                        </div>
                        <div class="col-md-2">
                                {!!Form::text('armacao_distancia_pupilar', 'Distância pupilar')
                                ->value(isset($item->oticaOs) ? $item->oticaOs->armacao_distancia_pupilar : '')
                                !!}
                        </div>

                        <hr>
                        <h5>Altura do Centro Ótico</h5>
                        <div class="col-md-2">
                                {!!Form::text('armacao_altura_centro_longe_od', 'Longe OD')
                                ->value(isset($item->oticaOs) ? $item->oticaOs->armacao_altura_centro_longe_od : '')
                                !!}
                        </div>
                        <div class="col-md-2">
                                {!!Form::text('armacao_altura_centro_longe_oe', 'Longe OE')
                                ->value(isset($item->oticaOs) ? $item->oticaOs->armacao_altura_centro_longe_oe : '')
                                !!}
                        </div>
                        <div class="col-md-2">
                                {!!Form::text('armacao_altura_centro_perto_od', 'Perto OD')
                                ->value(isset($item->oticaOs) ? $item->oticaOs->armacao_altura_centro_perto_od : '')
                                !!}
                        </div>
                        <div class="col-md-2">
                                {!!Form::text('armacao_altura_centro_perto_oe', 'Perto OE')
                                ->value(isset($item->oticaOs) ? $item->oticaOs->armacao_altura_centro_perto_oe : '')
                                !!}
                        </div>
                </div>
        </div>
</div>





