<div class="row g-2 mt-3">

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
        
        <h5 class="text-muted fw-bold mb-0">
          Valor original R$ {{ __moeda($item->valor_original) }}
        </h5>

        <h5 class="text-success fw-bold mt-1">
          Valor promoção R$ {{ __moeda($item->valor) }}
        </h5>

        <h6 class="text-primary">
          Data de início {{ __data_pt($item->data_inicio, 0) }}
        </h6>
        <h6 class="text-primary">
          Data de fim {{ __data_pt($item->data_fim, 0) }}
        </h6>

        @if($item->status)
        <span class="badge bg-success mt-1">Ativo</span>
        @else
        <span class="badge bg-danger mt-1">Desativado</span>
        @endif
      </div>

      <div class="card-footer bg-transparent border-0 text-center" style="margin-top: -20px;">
        <form action="{{ route('promocao-produtos.destroy', $item->id) }}" method="post" id="form-{{$item->id}}">
          @method('delete')
          @can('promocao_produtos_edit')
          <a class="btn btn-warning btn-sm text-white" href="{{ route('promocao-produtos.edit', [$item->id]) }}">
            <i class="ri-pencil-fill"></i>
          </a>
          @endcan
          @csrf
          @can('promocao_produtos_delete')
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
