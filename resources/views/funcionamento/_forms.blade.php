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

    <div class="table-responsive-sm mt-3">
        <table class="table table-centered" id="table-horarios">
            <thead>
                <tr>
                    <th>Dia</th>
                    <th>Início</th>
                    <th>Fim</th>
                </tr>
            </thead>
            <tbody>
                @isset($item)

                @foreach($funcionamento as $key => $f)
                <tr>
                    <input type="hidden" name="dia[]" value="{{$f->dia_id}}">
                    <td>
                        {!!Form::text('', '')->attrs(['class' => ''])->readonly()
                        ->value(\App\Models\DiaSemana::getDiaStr($f->dia_id))
                        !!}
                    </td>
                    <td>
                        {!!Form::text('inicio[]', '')->attrs(['class' => 'timer'])->required()
                        ->value($f->inicioParse)
                        !!}
                    </td>
                    <td>
                        {!!Form::text('fim[]', '')->attrs(['class' => 'timer'])->required()
                        ->value($f->finalParse)
                        !!}
                    </td>
                </tr>
                @endforeach

                @endif
                
            </tbody>

        </table>
    </div>
    
    <hr>
    <div class="text-end">
        <button type="submit" class="btn btn-success px-5">Salvar</button>
    </div>
</div>

