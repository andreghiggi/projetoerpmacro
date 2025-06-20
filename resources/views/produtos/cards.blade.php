@foreach($produtos as $prod)
<div class="card-group col-xl-4 col-md-6 col-sm-6" onclick="addProdutos('{{ $prod->id }}')">
    <div class="row" style="border: 1px solid #DEE2E6; border-radius: 5px; margin-left: 0.1px; margin-top: 1px;">
        
        <img src="{{$prod->img}}" class="card-img-top mt-1" alt="..." style="opacity: 0.8; height: 140px; border-radius: 10px; width: 100vw;">
        
        <div class="row mt-2">
            <p class="text-center text-black">{{$prod->nome}}</p>
        </div>
        <div class="row">
            @if($prod->valor_unitario > 0)
            <p class="text-center text-success fw-bold">R$ {{ __moeda($prod->valor_unitario) }}</p>
            @else
            <p class="text-center text-success fw-bold">--</p>
            @endif
        </div>
    </div>
</div>
@endforeach
