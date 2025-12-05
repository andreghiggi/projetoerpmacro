<div class="table-responsive">
    <table class="table table-striped table-centered mb-0">
        <thead class="table-dark">
            <tr>
                <th></th>
                <th>#</th>
                <th>Produto</th>
                <th>Categoria</th>
                <th>Quantidade</th>
                <th>Valor de venda</th>
                <th>Unidade</th>
                @if(__countLocalAtivo() > 1)
                <th>Local</th>
                @endif
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $item)
            <tr>
                <td><img class="img-60" src="{{ $item->produto->img }}"></td>
                <td data-label="Código">{{ $item->produto->numero_sequencial }}</td>
                <td data-label="Descrição">
                    {{ $item->descricao() }}
                </td>
                <td data-label="Categoria">{{ $item->produto->categoria ? $item->produto->categoria->nome : '' }}</td>
                <td data-label="Quantidade">
                    @if(!$item->produto->unidadeDecimal())
                    {{ number_format($item->quantidade, 0, '.', '') }}
                    @else
                    {{ number_format($item->quantidade, 3, '.', '') }}
                    @endif
                </td>
                <td data-label="Variação">
                    @if($item->produtoVariacao)
                    {{ __moeda($item->produtoVariacao->valor) }}
                    @else
                    {{ __moeda($item->produto->valor_unitario) }}
                    @endif
                </td>
                <td data-label="Unidade">{{ $item->produto->unidade }}</td>
                @if(__countLocalAtivo() > 1)
                <td data-label="Local">{{ $item->local->descricao }}</td>
                @endif
                <td>
                    <form style="width: 200px;" action="{{ route('estoque.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
                        @method('delete')
                        @csrf
                        @can('estoque_edit')
                        <a title="Editar estoque" href="{{ route('estoque.edit', [$item->id]) }}" class="btn btn-dark btn-sm">
                            <i class="ri-edit-2-line"></i>
                        </a>
                        @endcan
                        @can('produtos_edit')
                        <a title="Editar produto" href="{{ route('produtos.edit', [$item->produto_id]) }}" class="btn btn-warning btn-sm">
                            <i class="ri-pencil-fill"></i>
                        </a>
                        @endcan

                        @can('estoque_delete')
                        <button type="button" class="btn btn-delete btn-sm btn-danger">
                            <i class="ri-delete-bin-line"></i>
                        </button>
                        @endcan

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
</div>