@extends('layouts.app', ['title' => 'Estoque por localização'])
@section('content')
<div class="card mt-1">
    <div class="card-header">
        <h4>Estoque por localização: <strong class="text-primary">{{ $item->nome }}</strong></h4>
        
        <hr>
    </div>
    <div class="card-body">
        {!!Form::open()
        ->put()
        ->route('estoque-localizacao.store', [$item->id])
        ->multipart()
        !!}
        <div class="pl-lg-4">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Local</th>
                            <th>Quantidade</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($item->locais as $local)
                        <tr>
                            <td>
                                <input type="hidden" readonly class="form-control" required name="local_id[]" value="{{ $local->localizacao->id }}">
                                <input readonly class="form-control" required value="{{ $local->localizacao->descricao }}">
                            </td>
                            <td>
                                <input class="form-control quantidade" required name="quantidade[]">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="col-12 mt-3" style="text-align: right;">
                <button type="submit" class="btn btn-success px-5">Salvar</button>
            </div>
        </div>
        {!!Form::close()!!}
    </div>
</div>
@endsection
