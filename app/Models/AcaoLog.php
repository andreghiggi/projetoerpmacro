<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcaoLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'local', 'acao', 'descricao'
    ];

    public function empresa(){
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public static function acoes(){
        return [
            '' => 'Selecione',
            'cadastrar' => 'cadastrar',
            'editar' => 'editar',
            'excluir' => 'excluir',
            'transmitir' => 'transmitir',
            'cancelar' => 'cancelar',
            'corrigir' => 'corrigir',
            'erro' => 'erro'
        ];
    }

    public static function locais(){
        return [
            '' => 'Selecione',
            'Produto' => 'Produto',
            'Categoria de Produto' => 'Categoria de Produto',
            'Estoque' => 'Estoque',
            'Variação de Produto' => 'Variação de Produto',
            'Lista de Preços' => 'Lista de Preços',
            'Padrão de Tributação' => 'Padrão de Tributação',
            'Marca' => 'Marca',
            'Modelo Etiqueta' => 'Modelo Etiqueta',
            'Transferência de Estoque' => 'Transferência de Estoque',
            'Categoria de Serviço' => 'Categoria de Serviço',
            'Serviço' => 'Serviço',
            'Ordem de Serviço' => 'Ordem de Serviço',
            'Ordem de Serviço - Serviço' => 'Ordem de Serviço - Serviço',
            'Ordem de Serviço - Produto' => 'Ordem de Serviço - Produto',
            'Agendamento' => 'Agendamento',
            'Usuário' => 'Usuário',
            'Cliente' => 'Cliente',
            'Fornecedor' => 'Fornecedor',
            'Funcionario' => 'Funcionario',
            'Evento Salário' => 'Evento Salário',
            'Orçamento' => 'Orçamento',
            'Compra' => 'Compra',
            'Venda' => 'Venda',
            'Importação XML' => 'Importação XML',
            'Devolução XML' => 'Devolução XML',
            'PDV' => 'PDV',
            'NFe' => 'NFe',
            'NFCe' => 'NFCe',
            'Conta a pagar' => 'Conta a Pagar',
            'Conta a receber' => 'Conta a Receber',
            'Caixa' => 'Caixa',
            'Taxa de Pagamento' => 'Taxa de Pagamento',
            'Conta para Empresa' => 'Conta para Empresa',
            'Conta para Boleto' => 'Conta para Boleto',
            'CTe' => 'CTe',
            'MDFe' => 'MDFe',
            'Unidade de Medida' => 'Unidade de Medida',
            'Tipo de Despesa de Frete' => 'Tipo de Despesa de Frete',
            'Frete' => 'Frete',
            'Manutenção de Veículo' => 'Manutenção de Veículo',
            'Inventário' => 'Inventário',
            'Convênio' => 'Convênio',
            'Médico' => 'Médico',
            'Laboratório' => 'Laboratório',
            'Tratamento Ótica' => 'Tratamento Ótica',
            'Tipo de Armação' => 'Tipo de Armação',
            'Formato de Armação' => 'Formato de Armação',
            'Configuração Usuário Emissão' => 'Configuração Usuário Emissão',
            'Meta' => 'Meta',
        ];
    }

}
