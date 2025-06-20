@extends('layouts.app', ['title' => 'Logs da API'])
@section('content')
<div class="mt-3">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div style="text-align: right; margin-top: -5px;">

                    <a href="{{ route('config-api.index') }}" class="btn btn-danger btn-sm px-3">
                        <i class="ri-arrow-left-double-fill"></i>Voltar
                    </a>
                </div>
                <hr class="mt-3">
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-2">
                            {!!Form::select('status', 'Status', ['' => 'Todos', 'sucesso' => 'Sucesso', 'erro' => 'Erro'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-2">
                            {!!Form::select('tipo', 'Tipo', ['' => 'Todos'] + App\Models\ApiConfig::acoes())
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3">
                            {!!Form::select('prefixo', 'Local', ['' => 'Todos'] + App\Models\ApiConfig::permissoes())
                            ->attrs(['class' => 'select2'])
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
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('config-api.logs') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>

                                    <th>Token</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th>Tipo</th>
                                    <th>Descrição</th>
                                    <th>Local</th>
                                    
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>

                                    <td>{{ $item->token }}</td>
                                    <td>{{ __data_pt($item->created_at) }}</td>

                                    <td>
                                        @if($item->status == 'sucesso')
                                        <i class="ri-checkbox-circle-fill text-success"></i>
                                        @else
                                        <i class="ri-close-circle-fill text-danger"></i>
                                        @endif
                                    </td>
                                    <td>{{ $item->getTipo() }}</td>
                                    <td>{{ $item->descricao }}</td>
                                    <td>{{ $item->getPrefixo() }}</td>

                                    
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

