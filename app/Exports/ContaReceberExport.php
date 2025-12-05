<?php
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ContaReceberExport implements FromView
{	
	protected $data;
	public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        return view('exports.contas_receber', [
            'data' => $this->data
        ]);
    }
}
