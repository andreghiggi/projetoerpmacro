@extends('layouts.app', ['title' => 'Sugestão e Desenvolvimento'])
@section('content')
<div class="mt-1">
    <div class="row">
        <div class="card">
            <div class="card-body">
                <div class="col-md-12">
                    <h5><i class="ri-code-s-slash-line"></i> Sugestão e Desenvolvimento</h5>
                </div>
                <hr class="mt-3">
                <div class="col-md-12">
                    <a href="{{ route('sugestao.create') }}" class="btn btn-success" style="float: right;">
                        <i class="ri-add-circle-fill"></i>
                        Nova Sugestão
                    </a>
                </div>
                <div class="col-lg-12">
                    {!!Form::open()->fill(request()->all())
                    ->get()
                    !!}
                    <div class="row mt-3">
                        <div class="col-md-2">
                            {!!Form::text('titulo', 'Pesquisar por título')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('sugestao.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3 table-responsive">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Criado por</th>
                                    <th>Título</th>
                                    <th>Data</th>
                                    <th>Comentários</th>
                                    <!-- <th>Visualizações</th> -->
                                    <th>Curtidas</th>
                                    <th>Estado</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->empresa->nome }}</td>
                                    <td>{{ $item->titulo }}</td>
                                    <td>{{ __data_pt($item->created_at) }}</td>
                                    <td>{{ $item->total_comentarios }}</td>
                                    <!-- <td>{{ $item->total_views }}</td> -->
                                    <td>
                                        <i class="ri-thumb-up-line"></i> {{ $item->total_votacoes }}
                                    </td>
                                    <td>
                                        @if($item->estado == 'nova')
                                        <h4><span class="badge bg-primary">NOVO</span></h4>
                                        @elseif($item->estado == 'recusada')
                                        <h4><span class="badge bg-danger">RECUSADA</span></h4>
                                        @elseif($item->estado == 'aprovada')
                                        <h4><span class="badge bg-success">APROVADA</span></h4>
                                        @elseif($item->estado == 'em_desevolvimento')
                                        <h4><span class="badge bg-info">EM DESENVOLVIMENTO</span></h4>
                                        @else
                                        <h4><span class="badge bg-dark">CONCLUÍDA</span></h4>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('sugestao.show', [$item->id]) }}" class="btn btn-dark" title="Visualizar">
                                            <i class="ri-file-list-2-line"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <br>
                        
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection


