@extends('layouts.app', ['title' => 'Galeria'])
@section('css')
<style type="text/css">

</style>
@endsection
@section('content')
<div class="mt-1 print">
    <div class="row">

        <div class="card">
            <div class="card-body">

                <!-- Invoice Logo-->
                <div class="clearfix">
                    <div class="float-start mb-3">
                        <img src="{{ $item->img }}" height="160">
                        <label>Imagem principal do produto</label>
                    </div>
                    <div class="float-end">
                        <h4 class="m-0">{{ $item->nome }}</h4>
                    </div>
                </div>
                {!!Form::open()
                ->post()
                ->route('produtos.galeria-store', [$item->id])
                ->multipart()
                ->id('form-image')
                !!}
                <div class="card col-md-3 mt-3 form-input" style="width: 210px">
                    <div class="preview">
                        <button type="button" id="btn-remove-imagem" class="btn btn-link-danger btn-sm btn-danger">x</button>

                        <img id="file-ip-1-preview" src="/imgs/no-image.png">
                    </div>
                    <label for="file-ip-1">Imagem</label>

                    <input type="file" id="file-ip-1" name="image" accept="image/*" onchange="showPreview(event);">
                    <button type="submit" id="btn-remove-imagem" class="btn btn-success mt-1 mb-1">SALVAR</button>

                </div>

                {!!Form::close()!!}

                <hr>
                <h4>Outras imagens do produto</h4>
                <div class="row mt-3">

                    @foreach($item->galeria as $i)
                    <div class="col-md-3 col-12">
                        <div class="card">
                            <div class="card-body" style="text-align: center;">
                                <img src="{{ $i->img }}" width="300">
                            </div>
                            <div class="card-footer">
                                <form action="{{ route('produtos.destroy-image', $i->id) }}" method="post" id="form-{{$i->id}}">
                                    @method('delete')
                                    @csrf
                                    <button type="button" class="btn btn-sm btn-danger btn-delete w-100">Remover imagem</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

