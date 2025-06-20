@extends('layouts.app', ['title' => 'Galeria '.$item->nome])
@section('css')
<style type="text/css">
    .img-nuvem-shop{
        height: 200px;
        width: 200px;
        border-radius: 10px;

    }
</style>
@endsection
@section('content')

<div class="card mt-1">
    <div class="card-header">
        <h4>Galeria Produto Nuvem Shop <strong class="text-danger">{{ $item->nome }}</strong></h4>
        <div style="text-align: right; margin-top: -35px;">
            <a href="{{ route('nuvem-shop-produtos.index') }}" class="btn btn-danger btn-sm px-3">
                <i class="ri-arrow-left-double-fill"></i>Voltar
            </a>
        </div>
    </div>
    <div class="card-body">
        <form class="row" action="{{ route('nuvem-shop-produtos-galery-store') }}" enctype="multipart/form-data" method="post">
            @csrf
            <input type="hidden" name="produto_id" value="{{ $item->id }}">
           
            <div class="col-md-3">
                <div class="card mt-3 form-input">
                    <div class="preview">
                        <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>
                        <img id="file-ip-1-preview" src="/imgs/no-image.png">
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

            @foreach($produto->images as $v => $g)
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body" style="text-align: center;">
                        <img class="img-nuvem-shop" src="{{ $g->src }}">
                    </div>
                    <div class="card-footer">
                        <form action="{{ route('nuvem-shop-produtos.galery-delete') }}">
                            <input type="hidden" name="produto_id" value="{{ $item->id }}">
                            <input type="hidden" name="imagem_id" value="{{ $g->id }}">
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
