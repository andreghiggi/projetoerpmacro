@extends('layouts.app', ['title' => 'Ingredientes para ' . $item->nome])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">            
            <div class="card-body">
                <h4>Ingredientes para o produto <strong class="text-success">{{ $item->nome }}</strong></h4>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()
                    ->post()
                    ->route('produtos-cardapio.store-ingrediente')
                    !!}
                    @csrf
                    <div class="row mt-3">
                        <div class="col-md-4">
                            {!!Form::text('ingrediente', 'Ingrediente')->required()
                            !!}
                        </div>
                        <input type="hidden" name="produto_id" value="{{ $item->id }}">

                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-success" type="submit">
                                <i class="ri-add-circle-fill"></i> Salvar
                            </button>
                        </div>
                    </div>

                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-centered">
                            <thead class="table-dark">
                                <tr>
                                    <th width="60%">Ingrediente</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->ingredientes as $a)
                                <tr>
                                    <td width="300">{{ $a->ingrediente }}</td>

                                    <td>
                                        <form action="{{ route('produtos-cardapio.destroy-ingrediente', $a->id) }}" method="post" id="form-{{$a->id}}">
                                            @method('delete')
                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
