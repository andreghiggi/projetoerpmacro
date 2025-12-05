@extends('layouts.app', ['title' => 'Interrupção'])
@section('content')

<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <h4>Intervalos (ex: horário de almoço, horário de café)</h4>
                <hr>
                <a class="btn btn-success px-3" href="{{ route('interrupcoes.create') }}">
                    <i class="ri-add-circle-fill"></i>
                    Nova Interrupção
                </a>

                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('funcionario_id', 'Funcionário', ['' => 'Todos'] + $funcionarios->pluck('nome', 'id')->all())
                            ->id('funcionario')
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('interrupcoes.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>

                <div class="table-responsive mt-3">
                    <table class="table table-striped table-centered mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Funcionário</th>
                                <th>Dia</th>
                                <th>Início</th>
                                <th>Fim</th>
                                <th>Motivo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data as $item)
                            <tr>
                                <td data-label="Funcionário">{{ $item->funcionario->nome }}</td>
                                <td data-label="Dia">{{ \App\Models\DiaSemana::getDiaStr($item->dia_id) }}</td>
                                <td data-label="Início">{{ isset($item) ? $item->inicioParse : '--' }}</td>
                                <td data-label="Fim">{{ isset($item) ? $item->finalParse : '--' }}</td>
                                <td data-label="Motivo">{{ $item->motivo }}</td>
                                <td data-label="Status">
                                    @if($item->status)
                                    <i class="ri-checkbox-circle-fill text-success"></i>
                                    @else
                                    <i class="ri-close-circle-fill text-danger"></i>
                                    @endif
                                </td>
                                <td>
                                    <form style="width: 100px;" action="{{ route('interrupcoes.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                        @csrf
                                        @method('delete')

                                        <a class="btn btn-warning btn-sm text-white" href="{{ route('interrupcoes.edit', [$item->id]) }}">
                                            <i class="ri-pencil-fill"></i>
                                        </a>
                                        
                                        <button type="submit" title="Deletar" class="btn btn-danger btn-delete btn-sm">
                                            <i class="ri-delete-bin-2-line"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Nada encontrado</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
                <div class="mt-3">
                    {!! $data->appends(request()->all())->links() !!}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
