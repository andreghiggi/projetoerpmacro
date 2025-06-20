@extends('layouts.app', ['title' => 'Interrupção'])
@section('content')

<div class="mt-3">
    <div class="row">
        {!!Form::open()
        ->post()
        ->route('interrupcoes.store')
        ->multipart()
        !!}
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h4>Novo intervalo</h4>
                    <hr>
                    <div class="row">
                        <div class="col-md-4 mt-3">
                            {!!Form::text('dia', 'Dia')->attrs([''])->value($item->dia)->readonly()
                            !!}
                        </div>
                        <input type="hidden" name="dia_id" value="{{ $item->id }}">
                        <div class="col-md-3 mt-3">
                            {!!Form::text('inicio', 'Início')->attrs(['data-mask' => '00:00'])
                            !!}
                        </div>
                        <div class="col-md-3 mt-3">
                            {!!Form::text('fim', 'Fim')->attrs(['data-mask' => '00:00'])
                            !!}
                        </div>
                        <div class="text-end mt-5">
                            <button type="submit" class="btn btn-info px-5">Salvar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>

@endsection
