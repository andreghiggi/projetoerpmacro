<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UpdateController extends Controller
{

    public function index()
    {
        return view('update.index');
    }

    public function sqlStore(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $text = file_get_contents($file);
            $lines = explode(";", $text);
            $logMessage = [];
            foreach ($lines as $sql) {
                if (trim($sql)) {
                    try {
                        \DB::unprepared("$sql;");
                        array_push($logMessage, "Comando SQL executado <strong class='text-info'>$sql;</strong>");
                    } catch (\Exception $e) {
                        array_push($logMessage, "Erro ao executar SQL: " . $e->getMessage() . " - <strong class='text-success'>ISSO NÃO AFETA A ATUALIZAÇÃO</strong>");
                    }
                }
            }
            return view('update.finish', compact('logMessage'))->with('title', 'Atualização');
        } else {
            session()->flash('flash_error', "Arquivo não foi selecionado!!");
            return redirect()->back();
        }
    }

    public function runSql(Request $request)
    {
        $sql = $request->sql;
        $lines = explode(";", $sql);
        $logMessage = [];

        foreach ($lines as $sql) {
            if (trim($sql)) {
                try {
                    \DB::unprepared("$sql;");
                    array_push($logMessage, "Comando SQL executado <strong class='text-info'>$sql;</strong>");
                } catch (\Exception $e) {
                    array_push($logMessage, "Erro ao executar SQL: " . $e->getMessage() . " - <strong class='text-success'>ISSO NÃO AFETA A ATUALIZAÇÃO</strong>");
                }
            }
        }
        return view('update.finish', compact('logMessage'))->with('title', 'Atualização');
    }

}
