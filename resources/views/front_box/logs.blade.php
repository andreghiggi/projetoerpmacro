@extends('layouts.app', ['title' => 'Logs de PDV'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">

                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-2">
                            {!!Form::text('usuario_id', 'Pesquisar por usuário')
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

                        <div class="col-md-2">
                            {!!Form::time('start_time', 'Horário inicial')
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::time('end_time', 'Horário final')
                            !!}
                        </div>

                        <div class="col-md-2">
                            {!!Form::select('acao', 'Ação',
                            [
                            '' => 'Todos',
                            'Item removido' => 'Item removido',
                            'Desconto' => 'Desconto',
                            'Acréscimo' => 'Acréscimo',
                            ])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('frontbox.logs') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>

                                    <th>Usuário</th>
                                    <th>Produto</th>
                                    <th>Ação</th>
                                    <th>Valor desconto</th>
                                    <th>Valor acréscimo</th>
                                    <th>Data</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($data as $item)
                                <tr>

                                    <td data-label="Usuário">{{ $item->usuario->name }}</td>
                                    <td data-label="Produto">{{ $item->produto ? $item->produto->nome : '' }}</td>
                                    <td data-label="Ação">{{ $item->acao }}</td>
                                    <td data-label="Valor desconto">{{ $item->valor_desconto ? __moeda($item->valor_desconto) : '' }}</td>
                                    <td data-label="Valor acréscimo">{{ $item->valor_acrescimo ? __moeda($item->valor_acrescimo) : '' }}</td>
                                    <td data-label="Data">{{ __data_pt($item->created_at) }}</td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>

                    </div>
                </div>
                <br>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script type="text/javascript" src="/js/delete_selecionados.js"></script>
@endsection
