@extends('layouts.app', ['title' => 'Adicionais para ' . $item->nome])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">            
            <div class="card-body">
                <h4>Adicionais para o produto <strong class="text-success">{{ $item->nome }}</strong></h4>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()
                    ->post()
                    ->route('produtos-cardapio.store-adicional')
                    !!}
                    @csrf
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('adicional_id', 'Adicional', ['' => 'Selecione'] + $adicionais->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'select2'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('categoria_id', 'Categoria', ['' => 'Selecione'] + $categorias->pluck('nome', 'id')->all())
                            ->attrs(['class' => 'select2'])
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
                                    <th>Adicional</th>
                                    <th>Valor</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item->adicionais as $a)
                                <tr>
                                    <td width="300">{{ $a->adicional->nome }}</td>
                                    <td>{{ __moeda($a->adicional->valor) }}</td>
                                    <td>
                                        <form action="{{ route('produtos-cardapio.destroy-adicional', $a->id) }}" method="post" id="form-{{$a->id}}">
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
