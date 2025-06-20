<div class="row">
    @foreach($data as $prod)
    <div class="card-group col-sm-6 col-md-3" onclick="selectPizza('{{ $prod->id }}')" style="margin-left: 5px">
        <div class="row bg-{{ $prod->id }} @if($prod->id == $produto_id) bg-info @endif" style="border: 1px solid rgb(209, 204, 204); border-radius: 7px;">

            <img src="{{$prod->img}}" class="card-img-top mt-1" alt="..." style="opacity: 0.8; height: 140px;">

            <div class="row mt-2">
                <p class="text-center text-black">{{$prod->nome}}</p>
            </div>
            <div class="row">
                <p class="text-center text-success fw-bold"> R$ {{ __moeda($prod->valorPizza($tamanho_id)) }}</p>
            </div>
        </div>
    </div>
    @endforeach
</div>
