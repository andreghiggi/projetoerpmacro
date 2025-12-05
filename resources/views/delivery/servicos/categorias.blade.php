@extends('layouts.app', ['title' => 'Categorias de Serviço Marketplace'])
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
                            {!!Form::text('nome', 'Pesquisar por nome')
                            !!}
                        </div>
                        <div class="col-md-3 text-left ">
                            <br>
                            <button class="btn btn-primary" type="submit"> <i class="ri-search-line"></i>Pesquisar</button>
                            <a id="clear-filter" class="btn btn-danger" href="{{ route('servico-marketplace.categorias') }}"><i class="ri-eraser-fill"></i>Limpar</a>
                        </div>
                    </div>
                    {!!Form::close()!!}
                </div>
                <div class="col-md-12 mt-3">
                    <div class="table-responsive-sm">
                        <table class="table table-striped table-centered mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nome</th>
                                    <th width="20%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data as $item)
                                <tr>
                                    <td>{{ $item->nome }}</td>
                                    
                                    <td>

                                        <a class="btn btn-warning btn-sm text-white" href="{{ route('categoria-servico.edit', [$item->id]) }}">
                                            <i class="ri-pencil-fill"></i>
                                        </a>

                                        <label class="switch">
                                            <input @if($item->marketplace) checked @endif type="checkbox" value="{{ $item->id }}" class="switch-check">
                                            <span class="slider round"></span>
                                        </label>

                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center">Nada encontrado</td>
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
        $.get(path_url + "api/servico-marketplace/switch-categoria", {id: id})
        .done((success) => {
        })
        .fail((err) => {
            console.log(err)
        })
    })
</script>
@endsection