<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Garantia #{{ $garantia->id }}</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 12px;
      color: #333;
      margin: 20px;
    }
    .header {
      text-align: center;
      border-bottom: 2px solid #555;
      margin-bottom: 20px;
    }
    .header h2 {
      margin: 0;
    }
    .info {
      margin-bottom: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 6px 8px;
      text-align: left;
    }
    th {
      background: #f3f3f3;
    }
    .status {
      font-weight: bold;
      text-transform: uppercase;
    }
  </style>
</head>
<body>

  <div class="header">
    @if($config->logo != null)
    <img style="margin-top: -45px; height: 80px;" src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('/uploads/logos/'. $config->logo)))}}" alt="Logo" class="mb-2">
    @else
    <img style="margin-top: -45px; height: 80px;" src="{{'data:image/png;base64,' . base64_encode(file_get_contents(@public_path('logo.png')))}}" alt="Logo" class="mb-2">
    @endif

    <h2>Comprovante de Garantia</h2>
    <small>Emitido em {{ date('d/m/Y') }}</small>
  </div>

  <div class="info">
    <strong>Cliente:</strong> {{ $garantia->cliente->razao_social }}<br>
    @if($garantia->produto)
    <strong>Produto:</strong> {{ $garantia->produto->nome }}<br>
    @elseif($garantia->servico)
    <strong>Serviço:</strong> {{ $garantia->servico->nome }}<br>
    @endif
    <strong>Data da Venda:</strong> {{ $garantia->data_venda ? date('d/m/Y', strtotime($garantia->data_venda)) : '--' }}<br>
    <strong>Prazo de Garantia:</strong> {{ $garantia->prazo_garantia }} dias<br>
  </div>

  @if($garantia->observacao)
  <table>
    <tr>
      <th>Observações</th>
    </tr>
    <tr>
      <td>{{ $garantia->observacao }}</td>
    </tr>
  </table>
  @endif

  @if($garantia->valor_reparo)
  <div style="margin-top:20px;">
    <strong>Valor de Reparo:</strong> R$ {{ __moeda($garantia->valor_reparo) }}
  </div>
  @endif

</body>
</html>
