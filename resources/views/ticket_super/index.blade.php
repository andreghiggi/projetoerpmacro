@extends('layouts.app', ['title' => 'Solicitações'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-4">
                    <a href="{{ route('ticket-super.create') }}" class="btn btn-success">
                        <i class="ri-add-circle-fill"></i>
                        Novo chamado
                    </a>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-3">
                            {!!Form::select('empresa', 'Empresa')
                            ->options($empresa != null ? [$empresa->id => $empresa->info] : [])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::date('created_at', 'Data de criação')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('status', 'Status', ['' => 'Todos', 'aberto' => 'Aberto', 'respondida' => 'Respondida', 'aguardando' => 'Aguardando', 'resolvido' => 'Resolvido'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('departamento', 'Departamento', ['' => 'Selecione', 'financeiro' => 'Financeiro', 'suporte' => 'Suporte'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('ticket-super.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input" type="checkbox" id="select-all-checkbox">
                                        </div>
                                    </th>
                                    <th>Empresa</th>
                                    <th>Assunto</th>
                                    <th>ID</th>
                                    <th>Departamento</th>
                                    <th>Data de criação</th>
                                    <th>Última atividade</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>
                                        <div class="form-check form-checkbox-danger mb-2">
                                            <input class="form-check-input check-delete" type="checkbox" name="item_delete[]" value="{{ $item->id }}">
                                        </div>
                                    </td>
                                    <td>{{ $item->empresa->nome }}</td>
                                    <td>{{ $item->assunto }}</td>
                                    <td>#{{ $item->id }}</td>
                                    <td>{{ strtoupper($item->departamento) }}</td>
                                    <td>{{ __data_pt($item->created_at) }}</td>
                                    <td>{{ __data_pt($item->updated_at) }}</td>
                                    <td>
                                        @if($item->status == 'aberto')
                                        <span class="p-1 bg-dark rounded text-white">aberto</span>
                                        @elseif($item->status == 'respondida')
                                        <span class="p-1 bg-warning rounded text-white">respondida</span>
                                        @elseif($item->status == 'aguardando')
                                        <span class="p-1 bg-danger rounded text-white">aguardando</span>
                                        @else
                                        <span class="p-1 bg-success rounded text-white">resolvido</span>
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('ticket-super.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            <a title="Visualizar ticket" class="btn btn-dark btn-sm" href="{{ route('ticket-super.show', [$item->id]) }}">
                                                <i class="ri-file-text-fill"></i>
                                            </a>
                                            <a class="btn btn-warning btn-sm" href="{{ route('ticket-super.edit', [$item->id]) }}">
                                                <i class="ri-edit-line"></i>
                                            </a>
                                            @csrf
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <br>
                        <form action="{{ route('ticket-super.destroy-select') }}" method="post" id="form-delete-select">
                            @method('delete')
                            @csrf
                            <div></div>
                            <button type="button" class="btn btn-danger btn-sm btn-delete-all" disabled>
                                <i class="ri-close-circle-line"></i> Remover selecionados
                            </button>
                        </form>
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript" src="/js/delete_selecionados.js"></script>
@endsection
