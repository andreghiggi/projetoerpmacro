<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicaoReceitaOs extends Model
{
    use HasFactory;

    protected $fillable = [
        'ordem_servico_id', 'esferico_longe_od', 'esferico_longe_oe', 'esferico_perto_od', 'esferico_perto_oe',
        'cilindrico_longe_od', 'cilindrico_longe_oe', 'cilindrico_perto_od', 'cilindrico_perto_oe',
        'eixo_longe_od', 'eixo_longe_oe', 'eixo_perto_od', 'eixo_perto_oe', 'altura_longe_od', 'altura_longe_oe',
        'altura_perto_od', 'altura_perto_oe', 'dnp_longe_od', 'dnp_longe_oe', 'dnp_perto_od', 'dnp_perto_oe'
    ];

}
