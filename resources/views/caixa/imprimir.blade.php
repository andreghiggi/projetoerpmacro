<!DOCTYPE html>
<html>
<head>
    <title></title>
    <!--  -->
    <style type="text/css">
        .content {
            margin-top: -30px;
        }

        .titulo {
            font-size: 20px;
            margin-bottom: 0px;
            font-weight: bold;
        }

        .b-top {
            border-top: 1px solid #000;
        }

        .b-bottom {
            border-bottom: 1px solid #000;
        }

        .page_break {
            page-break-before: always;
        }

        td {
            font-size: 12px;
        }

        td strong {
            color: #666876;
        }

        .logoBanner img {
         float: left;
         max-width: 70px;
     }

     *{
        font-family: "Lucida Console", "Courier New", monospace;
    }

</style>
</head>
<header>
	<div class="headReport" style="display:flex; justify-content:  padding-top:1rem">
		<img style="margin-top: -75px;" src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('logo.png')))}}" alt="Logo" class="mb-2" height="160px">

		<div class="row text-right">
			<div class="col-12" style="margin-top: -50px;">
				<small class="float-right" style="color:grey; font-size: 11px;">Emissão:
				{{ date('d/m/Y - H:i') }}</small><br>
			</div>
		</div>

		<div class="row">
			<h4 style="text-align:center; margin-top: -50px;">Relátorio de Caixa</h4>
		</div>
		
	</div>
</header>
<body>
    {{-- <div class="content">
        <table>
            <tr>
                <img style="margin-top: -75px;" src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('logo.png')))}}" alt="Logo" class="mb-2" height="290">
                <br>
                <td class="" style="width: 100px;">
                    <label class="titulo">Relátorio de Caixa</label>
                </td>
                <td>
                    <strong>
                        Emissão: {{ date('d/m/Y H:i') }}
                    </strong>
                </td>
            </tr>
        </table>
    </div> --}}

    <br>

    <table>
        <tr>
            <td class="" style="width: 500px;">
                Razão social: <strong>{{ $config->nome_fantasia }}</strong>
            </td>
            <td class="" style="width: 197px;">
                Documento: <strong>{{ str_replace(" ", "", $config->cpf_cnpj) }}</strong>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td class="" style="width: 333px;">
                Total de vendas: <strong>{{ number_format($totalVendas, 2, ',', '.') }}</strong>
            </td>
            <td class="" style="width: 333px;">
                Data de abertura: <strong>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</strong>
            </td>
            <td class="" style="width: 333px;">
                Data de fechamento: <strong>{{ \Carbon\Carbon::parse($item->updated_at)->format('d/m/Y H:i') }}</strong>
            </td>
        </tr>
        <tr>
            <td class="" style="width: 333px;">
                Total de compras: <strong>{{ number_format($totalCompras, 2, ',', '.') }}</strong>
            </td>
        </tr>
    </table>
    @php $valorEmDinheiro = 0; @endphp
    <table>
        <tr>
            <td class="b-top" style="width: 700px;">
                Total por tipo de pagamento:
            </td>
        </tr>
    </table>
    <table>
        @foreach($somaTiposPagamento as $key => $tp)
        @if($tp > 0)
        <tr>
            <td class="b-top" style="width: 400px;">
                {{App\Models\Nfce::getTipoPagamento($key)}}
            </td>
            <td class="b-top" style="width: 300px;">
                <strong>R$ {{number_format($tp, 2, ',', '.')}}</strong>
            </td>
        </tr>
        @php if($key == '01') $valorEmDinheiro = $tp; @endphp
        @endif

        @endforeach
    </table>

    <table style="margin-top: 20px">
        <thead>
            <tr>
                <td width="180">CLIENTE</td>
                <td>DATA</td>
                <td>TIPO DE PAGAMENTO</td>
                <td>ESTADO</td>
                <td>NFCE/NFE</td>
                <td>TIPO</td>
                <td>VALOR</td>
                <td>DESCONTO</td>
            </tr>
        </thead>

        <tbody>

            @foreach($data as $v)
            <tr>
                <td class="b-top">{{ $v->cliente->razao_social ?? 'NÃO IDENTIFCADO' }}</td>
                <td class="b-top">{{ \Carbon\Carbon::parse($v->created_at)->format('d/m/Y H:i:s')}}</td>
                <td class="b-top">
                    @if($v->tipo_pagamento == '99')
                    Outros
                    @else
                    {{ $v->tipo_pagamento ? $v->getTipoPagamento($v->tipo_pagamento) : 'Pag Múltiplo'}}
                    @endif
                </td>
                <td class="b-top">{{ $v->tipo != 'OS' ? $v->estado : '' }}</td>

                @if($v->estado == 'aprovado')
                @if($v->tipo == 'Nfe')
                <td class="b-top">
                    {{ $v->numero > 0 ? $v->numero : '--' }}
                </td>
                @else
                <td class="b-top">
                    {{ $v->numero > 0 ? $v->numero : '--' }}
                </td>
                @endif
                @else
                <td class="b-top">--</td>
                @endif

                <td class="b-top" style="width: 100px">{{ $v->tipo }}</td>
                @if($v->tipo != 'OS')
                <td class="b-top" style="width: 100px">{{ __moeda($v->total) }}</td>
                @else
                <td class="b-top" style="width: 100px">{{ __moeda($v->valor) }}</td>
                @endif

                <td class="b-top" style="width: 100px">{{ __moeda($v->desconto) }}</td>
            </tr>


            @endforeach
        </tbody>
    </table>

    @php
    $somaSuprimento = 0;
    $somaSangria = 0;
    @endphp
    <br>

    <table>
        <tr>
            <td class="b-bottom" style="width: 350px;">
                Suprimentos
            </td>
        </tr>
    </table>
    @if(sizeof($suprimentos) > 0)
    @foreach($suprimentos as $s)

    @php
    $somaSuprimento += $s->valor;
    @endphp 
    <table>
        <tr>
            <td class="b-bottom" style="width: 200px;">
                {{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y H:i') }}
            </td>
            <td class="b-bottom" style="width: 300px;">
                {{$s->observacao}}
            </td>
            <td class="b-bottom" style="width: 200px;">
                R$ {{number_format($s->valor, 2, ',', '.')}}
            </td>
        </tr>
    </table>
    @endforeach
    @else
    <table>
        <tr>
            <td class="b-bottom" style="width: 700px;">
                R$ 0,00
            </td>
        </tr>
    </table>
    @endif
    <br>
    <table>
        <tr>
            <td class="b-bottom" style="width: 350px;">
                Sangrias
            </td>
        </tr>
    </table>
    @if(sizeof($sangrias) > 0)
    @foreach($sangrias as $s)

    @php
    $somaSangria += $s->valor;
    @endphp
    <table>
        <tr>
            <td class="b-bottom" style="width: 200px;">
                {{ \Carbon\Carbon::parse($s->created_at)->format('d/m/Y H:i') }}
            </td>
            <td class="b-bottom" style="width: 300px;">
                {{$s->observacao}}
            </td>
            <td class="b-bottom" style="width: 200px;">
                R$ {{number_format($s->valor, 2, ',', '.')}}
            </td>
        </tr>
    </table>
    @endforeach
    @else
    <table>
        <tr>
            <td class="b-bottom" style="width: 700px;">
                R$ 0,00
            </td>
        </tr>
    </table>
    @endif
    <br>
    <table>
        <tr>
            <td class="b-bottom" style="width: 233px;">
                Soma de vendas: <strong>R$ {{number_format($totalVendas, 2, ',', '.')}}</strong>
            </td>
            <td class="b-bottom" style="width: 233px;">
                Soma de sangria: <strong>R$ {{number_format($somaSangria, 2, ',', '.')}}</strong>
            </td>
            <td class="b-bottom" style="width: 233px;">
                Soma de suprimento: <strong>R$ {{number_format($somaSuprimento, 2, ',', '.')}}</strong>
            </td>
        </tr>

        <tr>
            <td class="b-bottom" style="width: 233px;">
                Valor em caixa: <strong>R$ {{number_format($somaSuprimento + $totalVendas - $somaSangria, 2, ',', '.')}}</strong>
            </td>
            <td class="b-bottom" style="width: 233px;">
                Contagem da gaveta: <strong>R$ {{number_format($item->valor_dinheiro, 2, ',', '.')}}</strong>
            </td>
            <td class="b-bottom" style="width: 233px;">
                Soma de serviços: <strong>R$ {{number_format($somaServicos, 2, ',', '.')}}</strong>
            </td>
        </tr>
    </table>

    <h4>Produtos vendidos</h4>
    <table style="margin-top: -20px">
        <thead>
            <tr>
                <td width="280">PRODUTO</td>
                <td width="100">QUANTIDADE</td>
                <td width="120">VALOR DE VENDA</td>
                <td width="120">VALOR DE COMPRA</td>
            </tr>
        </thead>
        <tbody>
            @foreach($produtos as $p)
            <tr>
                <td class="b-top">{{ $p['nome'] }}</td>
                <td class="b-top">{{ $p['quantidade'] }}</td>
                <td class="b-top">{{ __moeda($p['valor_venda']) }}</td>
                <td class="b-top">{{ __moeda($p['valor_compra']) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br><br>
    <table>
        <tr>
            <td class="" style="width: 300px;">
                ________________________________________
            </td>
        </tr>
        <tr>
            <td class="" style="width: 300px;">
                {{$usuario->name}} - {{ date('d/m/Y H:i') }}
            </td>
        </tr>
    </table>

</body>
