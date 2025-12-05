@extends('layouts.app', ['title' => 'Avaliações'])
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
                        <div class="col-md-4">
                            {!!Form::text('nome', 'Pesquisar por cliente')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('avaliacao-cardapio.index') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Cliente</th>
                                    <th>Telefone</th>
                                    <th>Avaliação</th>
                                    <th>Observação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>{{ $item->pedido ? $item->pedido->cliente_nome : '--' }}</td>
                                    <td>{{ $item->pedido ? $item->pedido->cliente_telefone : '' }}</td>
                                    <td>
                                        @for($i=1;$i<=5;$i++)
                                        @if($item->avaliacao >= $i)
                                        <i class="ri-star-fill text-warning"></i>
                                        @else
                                        <i class="ri-star-line"></i>
                                        @endif
                                        @endfor
                                    </td>
                                    <td>{{ $item->observacao }}</td>
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center">Nada encontrado</td>
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

@section('js')
<script type="text/javascript">

    $('.switch-check').on("click", function () {
        let id = $(this).val()
        $.get(path_url + "api/cardapio/switch-categoria", {id: id})
        .done((success) => {
        })
        .fail((err) => {
            console.log(err)
        })
    })
</script>
@endsection