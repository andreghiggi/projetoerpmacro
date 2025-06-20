<table>
    <thead>
        <tr>
            <th style="width: 300px">NOME</th>
            <th style="width: 300px">CATEGORIA</th>
            <th style="width: 120px">VALOR DE VENDA</th>
            <th style="width: 120px">VALOR DE COMPRA</th>
            <th style="width: 120px">NCM</th>
            <th style="width: 150px">CÓDIGO DE BARRAS</th>
            <th style="width: 120px">CEST</th>
            <th style="width: 120px">CST/CSOSN</th>
            <th style="width: 120px">CST PIS</th>
            <th style="width: 120px">CST COFINS</th>
            <th style="width: 120px">CST IPI</th>
            <th style="width: 120px">% RED BC</th>
            <th style="width: 120px">ORIGEM</th>
            <th style="width: 120px">CÓDIGO ENQ. IPI</th>
            <th style="width: 120px">CFOP ESTADUAL</th>
            <th style="width: 120px">CFOP OUTRO ESTADO</th>
            <th style="width: 120px">CÓDIGO BENEFICIO</th>
            <th style="width: 120px">UNIDADE</th>
            <th style="width: 120px">ORIGEM</th>
            <th style="width: 120px">GERENCIAR ESTOQUE</th>
            <th style="width: 120px">%ICMS</th>
            <th style="width: 120px">%PIS</th>
            <th style="width: 120px">%COFINS</th>
            <th style="width: 120px">%IPI</th>
            <th style="width: 120px">CFOP ENTRADA ESTADUAL</th>
            <th style="width: 120px">CFOP ENTRADA OUTRO ESTADO</th>
            <th style="width: 120px">ESTOQUE</th>
            <th style="width: 120px">ESTOQUE MÍNIMO</th>
            <th style="width: 120px">REFERÊNCIA</th>

        </tr>
    </thead>
    <tbody>
        @foreach($data as $key => $item)
        <tr>
            <td>{{ $item->nome }}</td>
            <td>{{ $item->categoria ? $item->categoria->nome : '' }}</td>
            <td>{{ $item->valor_unitario }}</td>
            <td>{{ $item->valor_compra }}</td>
            <td>{{ $item->ncm }}</td>
            <td>{{ $item->codigo_barras }}</td>
            <td>{{ $item->cest }}</td>
            <td>{{ $item->cst_csosn }}</td>
            <td>{{ $item->cst_pis }}</td>
            <td>{{ $item->cst_cofins }}</td>
            <td>{{ $item->cst_ipi }}</td>
            <td>{{ $item->perc_red_bc }}</td>
            <td>{{ $item->origem }}</td>
            <td>{{ $item->cEnq }}</td>
            <td>{{ $item->cfop_estadual }}</td>
            <td>{{ $item->cfop_outro_estado }}</td>
            <td>{{ $item->codigo_beneficio_fiscal }}</td>
            <td>{{ $item->unidade }}</td>
            <td>{{ $item->origem }}</td>
            <td>{{ $item->gerenciar_estoque }}</td>
            <td>{{ $item->perc_icms }}</td>
            <td>{{ $item->perc_pis }}</td>
            <td>{{ $item->perc_cofins }}</td>
            <td>{{ $item->perc_ipi }}</td>
            <td>{{ $item->cfop_entrada_estadual }}</td>
            <td>{{ $item->cfop_entrada_outro_estado }}</td>
            <td>{{ $item->estoque ? $item->estoque->quantidade : '0' }}</td>
            <td>{{ $item->estoque_minimo }}</td>
            <td>{{ $item->referencia }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
