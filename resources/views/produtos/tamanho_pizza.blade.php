@extends('layouts.app', ['title' => 'Valores Pizza '])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h3>Valores para <strong class="text-success">{{ $produto->nome }}</strong></h3>
                {!!Form::open()
                ->put()
                ->route('produtos.setar-tamanhos-valores', [$produto->id])
                !!}

                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tamanho</th>
                                    <th>Valor</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($tamanhos as $t)
                                <tr>
                                    <td>
                                        <input type="hidden" name="tamanho_id[]" value="{{ $t->id }}">
                                        <input type="text" readonly name="tamanho" value="{{ $t->nome }}" class="form-control">
                                    </td>
                                    
                                    <td class="col-2">
                                        <input required type="tel" name="valor[]" class="form-control moeda" 
                                        value="{{ $t->getValorDaPizza($produto->id) }}">
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        Nada encontrado

                                        <a href="{{ route('tamanhos-pizza.index') }}">cadastrar tamanhos de pizza</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="col-12" style="text-align: right;">
                        <button type="submit" class="btn btn-success btn-salvar-nfe px-5 m-3">Salvar</button>
                    </div>
                </div>
                {!!Form::close()!!}

            </div>
        </div>
    </div>
</div>


@endsection
