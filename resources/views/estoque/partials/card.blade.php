<div class="row g-2 mt-3">
  <h6>Total de registros: <strong>{{ $data->total() }}</strong></h6>

  @foreach ($data as $item)
  <div class="col-md-3 col-xl-2 col-sm-4 col-12">
    <div class="card border-0 shadow-sm h-100 produto-card">

      <div class="img-wrapper">
        <img src="{{ $item->produto->img }}" class="card-img-top produto-img" alt="{{ $item->nome }}">
      </div>

      <div class="card-body text-center">
        <h6 class="card-title fw-bold text-dark mb-1">
          <div class="form-check form-checkbox-danger mb-2">
            {{ $item->produto->nome }}
          </div>
        </h6>
        
        <h5 class="text-success fw-bold mb-0">
          R$ @if($item->produtoVariacao)
          {{ __moeda($item->produtoVariacao->valor) }}
          @else
          {{ __moeda($item->produto->valor_unitario) }}
          @endif
        </h5>

        <h5 class="text-muted fw-bold mb-0">
          Quantidade: @if(!$item->produto->unidadeDecimal())
          {{ number_format($item->quantidade, 0, '.', '') }}
          @else
          {{ number_format($item->quantidade, 3, '.', '') }}
          @endif
        </h5>

        @if($item->produto->status)
        <span class="badge bg-success mt-1">Ativo</span>
        @else
        <span class="badge bg-danger mt-1">Desativado</span>
        @endif
      </div>

      <div class="card-footer bg-transparent border-0 text-center" style="margin-top: -20px;">
        <form action="{{ route('estoque.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
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
      </div>
    </div>
  </div>


  @endforeach
</div>
