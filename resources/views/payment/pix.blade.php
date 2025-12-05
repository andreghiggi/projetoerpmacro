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
        <p class="text-center text-primary">Escaneie ou copie o código para efetuar o pagamento do plano, permaneça nesta tela até finalizar o processo!</p>
        <div class="col-lg-4 offset-lg-4 text-center">
            <img style="width: 400px; height: 400px;" src="data:image/jpeg;base64,{{$item->qr_code_base64}}"/>
        </div>
        <div class="input-group">
            <input type="text" class="form-control" value="{{$item->qr_code}}" id="qrcode_input" />

            <div class="input-group-append">
                <span class="input-group-text">
                    <i onclick="copy()" class="ri-file-copy-line">
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
        let transacao_id = $('#transacao_id').val();

        $.ajax({
            url: path_url + 'api/paymentStatus/'+'{{$item->transacao_id}}',
            method: "GET",
            global: false,
            data: {empresa_id: $('#empresa_id').val()}
        }).done((success) => {
            // console.log(success)
            if(success == "approved"){
                clearInterval(myInterval)
                swal("Sucesso", "Pagamento aprovado", "success").then(() => {
                    location.href = path_url
                })
            }
            
        }).fail((err) => {
        });

    }, 3000)

</script>

@endsection
