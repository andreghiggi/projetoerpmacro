<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\UsuarioEmpresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportarBalancaController extends Controller
{
    // Método para exportar para Toledo MGV5
    public function exp_bal_toledo_mgv5()
    {
        return $this->exportar('toledo_mgv5');
    }

    // Método para exportar para Toledo MGV6
    public function exp_bal_toledo_mgv6()
    {
        return $this->exportar('toledo_mgv6');
    }

    // Método para exportar para Filizola
    public function exp_bal_filizola()
    {
        return $this->exportarFilizola();
    }

    // Método comum para exportação, aceitando o modelo (toledo_mgv5, toledo_mgv6, filizola)
    private function exportar($modelo)
    {	
        // Obtém o ID da empresa do usuário autenticado
        $empresaId = auth()->user()->empresa->empresa_id;

        // Consulta os produtos filtrando pela empresa
        $produtos = Produto::where('exportar_balanca', 1)
        ->where('empresa_id', $empresaId)
        ->get();

        // Nome do arquivo com base no modelo
        if ($modelo == 'toledo_mgv5') {
            $arquivo = 'TXITENS.TXT';
        } elseif ($modelo == 'toledo_mgv6') {
            $arquivo = 'ITENSMGV.TXT';
        } elseif ($modelo == 'filizola') {
            // Este caso agora chama o exportarFilizola para gerar dois arquivos
            return $this->exportarFilizola();
        } else {
            return response()->json(['error' => 'Modelo desconhecido'], 400);
        }

        // Cria o arquivo para escrita no diretório de storage
        $file = fopen(storage_path("app/public/{$arquivo}"), 'w');

        // Gera cada linha do arquivo com base no modelo
        foreach ($produtos as $produto) {
            // Chama a função adequada para o modelo escolhido
            if ($modelo == 'toledo_mgv5') {
                $linha = $this->gerarLinhaToledoMGV5($produto);
            } elseif ($modelo == 'toledo_mgv6') {
                $linha = $this->gerarLinhaToledoMGV6($produto);
            } elseif ($modelo == 'filizola') {
                // Chamando o método correto para Filizola
                $linha = $this->gerarLinhaFilizola($produto);
            }

            // Escreve a linha no arquivo
            fwrite($file, $linha);
        }

        // Fecha o arquivo
        fclose($file);

        // Retorna uma resposta de sucesso, fazendo o download do arquivo gerado
        return response()->download(storage_path("app/public/{$arquivo}"));
    }

    // Geração da linha para o modelo Toledo MGV5
    private function gerarLinhaToledoMGV5($produto)
    {
        $departamento = str_pad($produto->categoria_id, 2, "0", STR_PAD_LEFT);
        $tipo_etiqueta = str_pad($produto->padrao_id, 2, "0", STR_PAD_LEFT);
        $tipo_preco = ($produto->unidade == 'kg') ? '0' : '1';
        $codigo_produto = str_pad($produto->id, 6, "0", STR_PAD_LEFT);
        $preco_unitario = str_pad(intval($produto->valor_unitario * 100), 6, "0", STR_PAD_LEFT);
        $validade = str_pad($produto->alerta_validade, 3, "0", STR_PAD_LEFT);
        $descricao1 = str_pad(substr($produto->nome, 0, 50), 50, " ");
        $descricao2 = str_pad(substr($produto->nome, 50, 50), 50, " ");
        $info_extra = str_pad(substr($produto->observacao, 0, 50), 50, " ") . 
        str_pad(substr($produto->observacao2, 0, 50), 50, " ") .
        str_pad(substr($produto->observacao3, 0, 50), 50, " ") .
        str_pad(substr($produto->observacao4, 0, 50), 50, " ");

        return $departamento . $tipo_etiqueta . $tipo_preco . 
        $codigo_produto . $preco_unitario . $validade . 
        $descricao1 . $descricao2 . $info_extra . PHP_EOL;
    }

    // Geração da linha para o modelo Toledo MGV6
    private function gerarLinhaToledoMGV6($produto)
    {
        // Código do Departamento
        $codigoDepartamento = str_pad($produto->categoria_id, 2, '0', STR_PAD_LEFT);
        
        // Tipo de Produto: "0" para peso, "1" para unidade
        $tipoProduto = ($produto->unidade == 'kg') ? '0' : '1';
        
        // Código do Item
        $codigoItem = str_pad($produto->id, 6, '0', STR_PAD_LEFT);
        
        // Preço do produto (em centavos)
        $preco = str_pad(intval($produto->valor_unitario * 100), 6, '0', STR_PAD_LEFT);
        
        // Dias de validade (campo "VVV")
        $diasValidade = str_pad($produto->alerta_validade, 3, '0', STR_PAD_LEFT);
        
        // Descrição do Produto (primeira linha e segunda linha)
        $descricao1 = str_pad(substr($produto->nome, 0, 25), 25, ' ', STR_PAD_RIGHT);
        $descricao2 = str_pad(substr($produto->nome, 25, 25), 25, ' ', STR_PAD_RIGHT);
        
        // Código do Fornecedor (campo "CF")
        $codigoFornecedor = str_pad($produto->fornecedor_id, 4, '0', STR_PAD_LEFT);  // Exemplo
        
        // Código da Imagem do Item (campo "FFFF")
        $codigoImagem = str_pad($produto->codigo_imagem, 4, '0', STR_PAD_LEFT);  // Exemplo
        
        // Código do Campo Extra 1 e 2 (campo "CE1", "CE2")
        $campoExtra1 = str_pad($produto->campo_extra_1, 4, '0', STR_PAD_LEFT);  // Exemplo
        $campoExtra2 = str_pad($produto->campo_extra_2, 4, '0', STR_PAD_LEFT);  // Exemplo
        
        // Código de Fornecedor Associado (campo "CE3")
        $codigoFornecedorAssociado = str_pad($produto->fornecedor_associado_id, 4, '0', STR_PAD_LEFT);  // Exemplo
        
        // Preço Promocional (campo "PPPPPP")
        $precoPromocional = str_pad(intval($produto->preco_promocional * 100), 6, '0', STR_PAD_LEFT);
        
        // Percentual de Glaciamento (campo "PG")
        $percentualGlaciamento = str_pad(intval($produto->percentual_glaciamento * 100), 4, '0', STR_PAD_LEFT);
        
        // Sequência de departamentos associados (campo "DA")
        $sequenciaDepartamentos = "|0205|"; // Exemplo de associação com departamentos 2 e 5
        
        // Código do Som (campo "CS")
        $codigoSom = str_pad($produto->codigo_som, 4, '0', STR_PAD_LEFT);  // Exemplo
        
        // Imprime Data de Validade (campo "DV")
        $imprimeValidade = '1'; // Ou '0' dependendo da configuração
        
        // Formatação final da linha, combinando todos os campos
        $linha = $codigoDepartamento . $tipoProduto . $codigoItem . $preco . $diasValidade . 
        $descricao1 . $descricao2 . $codigoFornecedor . $codigoImagem . 
        $campoExtra1 . $campoExtra2 . $codigoFornecedorAssociado . $precoPromocional . 
        $percentualGlaciamento . $sequenciaDepartamentos . $codigoSom . $imprimeValidade . PHP_EOL;
        
        return $linha;
    }

	// Geração da linha para o modelo Filizola
    private function gerarLinhaFilizola($produto)
    {
		// Código do Produto (6 caracteres numéricos)
        $codigo_produto = str_pad($produto->id, 6, "0", STR_PAD_LEFT);

		// Tipo (P para peso, U para unidade)
        $tipo_peso = ($produto->unidade == 'kg') ? 'P' : 'U';

		// Descrição (máximo 22 caracteres)
		$descricao = substr($produto->nome, 0, 22);  // Garantir que tenha no máximo 22 caracteres
		$descricao = str_pad($descricao, 22, ' ');  // Preencher com espaços se necessário

		// Preço por quilo (7 caracteres numéricos)
		$valor_unitario = number_format($produto->valor_unitario, 2, '', '');  // R$ 22,00 vira 2200
		$valor_unitario = str_pad($valor_unitario, 7, "0", STR_PAD_LEFT);

		// Prazo de validade (3 caracteres numéricos)
		$validade = str_pad($produto->alerta_validade, 3, "0", STR_PAD_LEFT);

		// Montando a string conforme as especificações
		return $codigo_produto . $tipo_peso . $descricao . $valor_unitario . $validade . PHP_EOL;
	}
	
	public function exportarFilizola(Request $request)
	{
        // Obtém o ID da empresa do usuário autenticado
        $empresaId = auth()->user()->empresa->empresa_id;

        // Consulta os produtos filtrando pela empresa
        $produtos = Produto::where('exportar_balanca', 1)
        ->where('empresa_id', $empresaId)
        ->get();

		// Definição dos arquivos
        $arquivoCadTxt = 'CADTXT.TXT';
        $arquivoSetorTxt = 'SETORTXT.TXT';

        $pathCadTxt = storage_path("app/public/{$arquivoCadTxt}");
        $pathSetorTxt = storage_path("app/public/{$arquivoSetorTxt}");

		// Criando os arquivos
        $fileCadTxt = fopen($pathCadTxt, 'w');
        $fileSetorTxt = fopen($pathSetorTxt, 'w');

        foreach ($produtos as $produto) {
            fwrite($fileCadTxt, $this->gerarLinhaFilizola($produto));
            fwrite($fileSetorTxt, $this->gerarLinhaSetor($produto));
        }

        fclose($fileCadTxt);
        fclose($fileSetorTxt);

		// Obtém o parâmetro 'arquivo' da requisição
        $arquivoSolicitado = $request->route('arquivo', 'cad');

		// Verifica qual arquivo retornar
        if ($arquivoSolicitado === 'setor') {
            return response()->download($pathSetorTxt)->deleteFileAfterSend(true);
        }

        return response()->download($pathCadTxt)->deleteFileAfterSend(true);
    }

	// Geração da linha para o arquivo SETORTXT.TXT
    private function gerarLinhaSetor($produto)
    {
		// Verifique se o produto tem o setor_id e, caso não, forneça um valor padrão ou trate o erro
        $codigoSetor = isset($produto->setor_id) ? str_pad($produto->setor_id, 3, '0', STR_PAD_LEFT) : '000';

		// Verifique se está gerando a linha corretamente
        return $codigoSetor . PHP_EOL;
    }
}
