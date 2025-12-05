<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'razao_social', 'nome_fantasia', 'cpf_cnpj', 'ie', 'contribuinte', 'consumidor_final',
        'email', 'telefone', 'cidade_id', 'rua', 'cep', 'numero', 'bairro', 'complemento', 'status', 'uid',
        'senha', 'token', 'valor_cashback', 'nuvem_shop_id', 'valor_credito', 'limite_credito',
        'lista_preco_id', '_id_import', 'id_estrangeiro', 'codigo_pais', 'numero_sequencial', 'data_nascimento',
        'imagem'
    ];

    protected $appends = [ 'endereco', 'info' ];

    public function getImgAttribute()
    {
        if($this->imagem == ""){
            return "/imgs/no-client.png";
        }
        return "/uploads/clientes/$this->imagem";
    }

    public function getInfoAttribute()
    {
        return "$this->razao_social $this->cpf_cnpj";
    }

    public function getEnderecoAttribute()
    {
        return "$this->rua, $this->numero - $this->bairro";
    }

    public function cidade(){
        return $this->belongsTo(Cidade::class, 'cidade_id');
    }

    public function listaPreco(){
        return $this->belongsTo(ListaPreco::class, 'lista_preco_id');
    }

    public function vendas(){
        return $this->hasMany(Nfe::class, 'cliente_id');
    }

    public function enderecos(){
        return $this->hasMany(EnderecoDelivery::class, 'cliente_id')->with('bairro');
    }

    public function enderecosEcommerce(){
        return $this->hasMany(EnderecoEcommerce::class, 'cliente_id');
    }

    public function carrinhosDelivery(){
        return $this->hasMany(CarrinhoDelivery::class, 'cliente_id');
    }

    public function enderecosDelivery(){
        return $this->hasMany(EnderecoDelivery::class, 'cliente_id');
    }

    public function pedidosEcommerce(){
        return $this->hasMany(PedidoEcommerce::class, 'cliente_id')->orderBy('id', 'desc');
    }

    public function enderecoPrincipal(){
        return $this->hasOne(EnderecoDelivery::class, 'cliente_id')->with('bairro')->where('padrao', 1);
    }

    public function tributacao(){
        return $this->hasOne(TributacaoCliente::class, 'cliente_id');
    }

    public function fatura(){
        return $this->hasMany(FaturaCliente::class, 'cliente_id');
    }

    public function pedidos(){
        return $this->hasMany(PedidoDelivery::class, 'cliente_id')->orderBy('id', 'desc')
        ->with(['itens', 'motoboy', 'endereco']);
    }

    public function cashBacks(){
        return $this->hasMany(CashBackCliente::class, 'cliente_id')->orderBy('id', 'desc');
    }

    public function creditosCliente(){
        return $this->hasMany(CreditoCliente::class, 'cliente_id')->orderBy('id', 'desc');
    }

    public static function getClienteDelivery($hash){
        return Cliente::where('uid', $hash)->first();
    }

}
