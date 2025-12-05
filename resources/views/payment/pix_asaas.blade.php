@extends('layouts.app', ['title' => 'Pagamento'])
@section('css')
<style type="text/css">
    .input-group-text{
        height: 40px;
    }
</style>
@endsection

@section('content')
<div class="card">
    <div class="row mt-5 m-2">
        <h5 class="text-center text-primary">Valor do plano: R${{ __moeda($item->valor) }}</h5>
        <p class="text-center text-primary">Escaneie ou copie o código para efetuar o pagamento do plano, permaneça nesta tela até finalizar o processo!</p>
        <p class="text-center text-danger">Após o pagamento aguarde nessa tela até ser redirecionado!</p>

        <div class="col-lg-4 offset-lg-4 text-center">
            <img style="width: 400px; height: 400px;" src="data:image/jpeg;base64,{{$data['encodedImage']}}"/>
        </div>
        <div class="input-group">
            <input type="text" class="form-control" value="{{$data['payload']}}" id="qrcode_input" />

            <div class="input-group-append" onclick="copy()">
                <span class="input-group-text">
                    <i class="ri-file-copy-line">
                    </i>
                </span>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')

<script type="text/javascript">
    var myInterval;
    function copy(){
        const inputTest = document.querySelector("#qrcode_input");

        inputTest.select();
        document.execCommand('copy');

        swal("", "Código pix copiado!!", "success")
    }


    myInterval = setInterval(() => {
        console.clear()
        let json = {
            id: '{{ $data["id"] }}',
            plano_id: '{{ $item->id }}',
            empresa_id: $('#empresa_id').val()
        }
        // console.log(json)
        $.get(path_url+'api/payment-status-asaas', json)
        .done((success) => {
            console.log(success)
            if(success == "pago"){
                clearInterval(myInterval)
                swal("Sucesso", "Pagamento aprovado", "success").then(() => {
                    location.href = path_url
                })
            }
        })
        .fail((err) => {
            console.log(err)
        })
    }, 3000)
    

</script>

@endsection
