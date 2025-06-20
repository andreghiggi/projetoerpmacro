@extends('layouts.app', ['title' => 'Respondendo Pergunta #'.$item->_id])

@section('content')

<div class="card mt-1">
    <div class="card-header">

        <div style="text-align: right; margin-top: -5px;">
            <a href="{{ route('mercado-livre-perguntas.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>

    </div>
    <div class="card-body">

        <h5>Data: <strong>{{ __data_pt($item->data) }}</strong></h5>
        <h5>Anúncio: <strong>{{ $item->anuncio ? $item->anuncio->nome : '#'.$item->item_id }}</strong></h5>

        <p>Pergunta: <strong>{{ $item->texto }}</strong></p>
        {!!Form::open()
        ->put()
        ->route('mercado-livre-perguntas.update', [$item->id])
        !!}
        <div class="pl-lg-4">
            <div class="row g-2">
                <div class="col-md-12">
                    @if($item->status != 'ANSWERED')
                    {!!Form::textarea('resposta', 'Resposta')->required()
                    !!}
                    @else
                    {!!Form::textarea('resposta', 'Resposta')
                    ->value($item->resposta)
                    ->readonly()
                    !!}
                    @endif
                </div>
                <hr class="mt-4">
                @if($item->status != 'ANSWERED')
                <div class="col-12" style="text-align: right;">
                    <button type="submit" class="btn btn-success px-5" id="btn-store">Salvar</button>
                </div>
                @endif
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>

@endsection
