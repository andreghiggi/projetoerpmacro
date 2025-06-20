@extends('layouts.app', ['title' => 'Galeria do Produto'])
@section('css')
<style type="text/css">
    .img-ml{
        height: 200px;
        margin-left: auto;
        margin-right: auto;
        width: 50%;
    }
</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Galeria <strong>{{ $item->nome }}</strong></h4>


        <form class="row" action="{{ route('mercado-livre-produtos-galery-store') }}" enctype="multipart/form-data" method="post">
            @csrf
            <input type="hidden" name="produto_id" value="{{ $item->id }}">
            @foreach($retorno->pictures as $i)
            <input type="hidden" name="picture[]" value="{{ $i->url }}">
            @endforeach
            <div class="col-md-3">
                <div class="card mt-3 form-input">
                    <div class="preview">
                        <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
                        @isset($item)
                        <img id="file-ip-1-preview" src="{{ $item->img }}">
                        @else
                        <img id="file-ip-1-preview" src="/imgs/no-image.png">
                        @endif
                    </div>
                    <label for="file-ip-1">Nova Imagem</label>

                    <input type="file" id="file-ip-1" name="image" accept="image/*" onchange="showPreview(event);">
                </div>
            </div>
            <div class="col-md-12">

                <button class="btn btn-success">
                    <i class="ri-send-plane-fill"></i>
                    Enviar para plataforma
                </button>
            </div>
        </form>
        <hr>
        <div class="row mt-2">

            <h4>Imagens</h4>
            <p class="text-danger">Atenção a plataforma pode demorar para processar as imagens e aparecerá em branco, aguarde o processamento!</p>
            @foreach($retorno->pictures as $i)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body" style="text-align: center;">
                        <img class="img-ml" src="{{ $i->url }}">
                    </div>
                    <div class="card-footer">
                        <form action="{{ route('mercado-livre-produtos.galery-delete') }}">
                            <input type="hidden" name="produto_id" value="{{ $item->id }}">

                            @foreach($retorno->pictures as $pic)
                            @if($i->url != $pic->url)
                            <input type="hidden" name="picture[]" value="{{ $pic->url }}">
                            @endif
                            @endforeach
                            <button class="btn btn-danger w-100">
                                Remover
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection