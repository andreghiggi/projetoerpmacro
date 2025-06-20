<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnidadeMedida extends Model
{
    use HasFactory;

    protected $fillable = [
        'empresa_id', 'nome', 'status'
    ];

    public static function unidadesMedidaPadrao()
    {
        return [
            "AMPOLA" => "AMPOLA",
            "BALDE" => "BALDE",
            "BANDEJ" => "BANDEJ",
            "BARRA" => "BARRA",
            "BISNAG" => "BISNAG",
            "BLOCO" => "BLOCO",
            "BOBINA" => "BOBINA",
            "BOMB" => "BOMB",
            "CAPS" => "CAPS",
            "CART" => "CART",
            "CENTO" => "CENTO",
            "CJ" => "CJ",
            "CM" => "CM",
            "CM2" => "CM2",
            "CX" => "CX",
            "CX2" => "CX2",
            "CX3" => "CX3",
            "CX5" => "CX5",
            "CX10" => "CX10",
            "CX15" => "CX15",
            "CX20" => "CX20",
            "CX25" => "CX25",
            "CX50" => "CX50",
            "CX100" => "CX100",
            "DISP" => "DISP",
            "DUZIA" => "DUZIA",
            "EMBAL" => "EMBAL",
            "FARDO" => "FARDO",
            "FOLHA" => "FOLHA",
            "FRASCO" => "FRASCO",
            "GALAO" => "GALAO",
            "GF" => "GF",
            "GRAMAS" => "GRAMAS",
            "JOGO" => "JOGO",
            "KG" => "KG",
            "KIT" => "KIT",
            "LATA" => "LATA",
            "LITRO" => "LITRO",
            "M" => "M",
            "M2" => "M2",
            "M3" => "M3",
            "MILHEI" => "MILHEI",
            "ML" => "ML",
            "MWH" => "MWH",
            "PACOTE" => "PACOTE",
            "PALETE" => "PALETE",
            "PARES" => "PARES",
            "PC" => "PC",
            "POTE" => "POTE",
            "K" => "K",
            "RESMA" => "RESMA",
            "ROLO" => "ROLO",
            "SACO" => "SACO",
            "SACOLA" => "SACOLA",
            "TAMBOR" => "TAMBOR",
            "TANQUE" => "TANQUE",
            "TON" => "TON",
            "TUBO" => "TUBO",
            "UN" => "UN",
            "VASIL" => "VASIL",
            "VIDRO" => "VIDRO"
        ];
    }
}
