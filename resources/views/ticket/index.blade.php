@extends('layouts.app', ['title' => 'Minhas solicitações'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-4">
                    <a href="{{ route('ticket.create') }}" class="btn btn-success">
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
                            {!!Form::text('assunto', 'Assunto')
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
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('ticket.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Assunto</th>
                                    <th>ID</th>
                                    <th>Departamento</th>
                                    <th>Data de criação</th>
                                    <th>Última atividade</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td><a href="{{ route('ticket.show', [$item->id]) }}">{{ $item->assunto }}</a></td>
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
