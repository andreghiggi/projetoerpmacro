@extends('layouts.app', ['title' => 'Apuração Mensal'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <div class="">
                        @can('apuracao_mensal_create')
                        <a href="{{ route('apuracao-mensal.create') }}" class="btn btn-success">
                            <i class="ri-add-circle-fill"></i>
                            Nova Apuração
                        </a>
                        @endcan
                    </div>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('funcionario_id', 'Pesquisar funcionário')
                            ->options($funcionario ? [$funcionario->id => $funcionario->nome] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('start_date', 'Data inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('end_date', 'Data final')
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('apuracao-mensal.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Funcionário</th>
                                    <th>Data Registro</th>
                                    <th>Valor Final</th>
                                    <th>Mês/Ano</th>
                                    <th>Adicionado em Contas a Pagar</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td data-label="Funcionário">{{ $item->funcionario->nome }}</td>
                                    <td data-label="Data Registro">{{ __data_pt($item->created_at) }}</td>
                                    <td data-label="Valor Final">{{ __moeda($item->valor_final) }}</td>
                                    <td data-label="Mês/Ano">{{ $item->mes }}/{{ $item->ano }}</td>
                                    <td data-label="Adicionado em Contas a Pagar">
                                        @if($item->conta_pagar_id == 0)
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @else
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        <a class="btn btn-sm btn-dark" target="_blank" href="/conta-pagar/{{$item->conta_pagar_id}}/edit">
                                            ver conta
                                        </a>
                                        @endif
                                    </td>
                                    <td>
                                        <form style="width: 120px;" action="{{ route('apuracao-mensal.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')

                                            @if(!$item->conta_pagar_id)
                                            @can('conta_pagar_create')
                                            <a title="Gerar conta a pagar" class="btn btn-dark btn-sm" href="{{ route('apuracao-mensal.conta-pagar', [$item->id]) }}">
                                                <i class="ri-money-dollar-box-line"></i>
                                            </a>
                                            @endcan
                                            @endif

                                            @csrf
                                            @can('apuracao_mensal_delete')
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                            @endcan

                                            <a class="btn btn-dark btn-sm" href="{{ route('apuracao-mensal.show', [$item->id]) }}">
                                                <i class="ri-printer-line"></i>
                                            </a>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection