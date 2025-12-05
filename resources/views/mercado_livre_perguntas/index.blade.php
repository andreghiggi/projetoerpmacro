@extends('layouts.app', ['title' => 'Perguntas Mercado Livre'])
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
                            {!!Form::select('status', 'Status', ['UNANSWERED' => 'AGUARDANDO RESPOSTA',
                            'ANSWERED' => 'RESPONDIDA'])
                            ->attrs(['class' => 'form-select'])
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('mercado-livre-perguntas.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Anúncio</th>
                                    <th>Pergunta</th>
                                    <th>Data</th>
                                    <th>Status</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    
                                    <td>{{ $item->anuncio ? $item->anuncio->nome : '#'.$item->item_id }}</td>
                                    <td>{{ $item->texto }}</td>
                                    <td>{{ __data_pt($item->data) }}</td>
                                    <td>
                                        @if($item->status == 'UNANSWERED')
                                        AGUARDANDO RESPOSTA
                                        @elseif($item->status == 'ANSWERED')
                                        RESPONDIDA
                                        @else
                                        --
                                        @endif
                                    </td>
                                    <td>
                                        <form action="{{ route('mercado-livre-perguntas.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                                            @method('delete')
                                            @csrf

                                            <a title="Responder pergunta" class="btn btn-dark btn-sm text-white" href="{{ route('mercado-livre-perguntas.show', [$item->id]) }}">
                                                <i class="ri-question-answer-fill"></i>
                                            </a>
                                            <button type="button" class="btn btn-delete btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">Nada encontrado</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <br>
                        
                    </div>
                </div>
                {!! $data->appends(request()->all())->links() !!}
            </div>
        </div>
    </div>
</div>
@endsection

