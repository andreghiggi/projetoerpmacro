@extends('layouts.app', ['title' => 'Funcionamento'])

@section('content')

<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Configurar os dias de funcionamento, horário de início e fim do expediente</h4>
                <hr>
                <a class="btn btn-success px-3" href="{{ route('funcionamentos.create') }}">
                    <i class="ri-add-circle-fill"></i>
                    Novo horário de funcionamento
                </a>

                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('funcionario_id', 'Pesquisar por funcionario')
                            ->options($funcionario != null ? [$funcionario->id => $funcionario->nome] : [])
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('funcionamentos.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="table-responsive-sm mt-3">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Funcionário</th>
                                <th>Dia</th>
                                <th>Início</th>
                                <th>Fim</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $item)
                            <tr>
                                <td>{{ $item->funcionario->nome }}</td>
                                <td>{{ $item->getDiaStr() }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->inicio)->format('H:i') }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->fim)->format('H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Nada encontrado</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <br>    
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>

@endsection
