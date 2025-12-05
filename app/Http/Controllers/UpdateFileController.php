<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SystemUpdate;

class UpdateFileController extends Controller
{
    public function index(){
        $update = SystemUpdate::orderBy('id', 'desc')->first();
        return view('update_file.index', compact('update'));
    }

    public function log(){
        $data = SystemUpdate::orderBy('id', 'desc')->get();
        return view('update_file.log', compact('data'));
    }

    public function setVersion(Request $request){
        $versao = $request->versao;
        $system = SystemUpdate::where('versao', $versao)->first();
        if($system == null){
            SystemUpdate::create(['versao' => $versao]);
        }
        return redirect()->back();
    }

    public function store(Request $request){
        $raiz = public_path();
        $raiz = substr($raiz, 0, strlen($raiz)-7);

        if (!is_dir("$raiz/temp-files")){
            mkdir("$raiz/temp-files", 0777, true);
        }

        $logMessage = [];
        $zip = new \ZipArchive();

        if ($zip->open($request->file) === TRUE) {
            $zip->extractTo("$raiz/temp-files");
            $zip->close();
            array_push($logMessage, "Arquivo zip extraido em /temp-files");
        }else{
            echo "Erro ao extrair arquivo";
            die;
        }

        if(is_file("$raiz/temp-files/new_tables_api.sql")){
            $lines = file_get_contents("$raiz/temp-files/new_tables_api.sql");
            $lines = explode(";", $lines);
            foreach($lines as $sql){
                if(trim($sql)){
                    try{
                        \DB::unprepared("$sql;");
                        array_push($logMessage, "Comando SQL executado <strong class='text-info'>$sql</strong>;");

                    }catch(\Exception $e){
                        // array_push($logMessage, "Erro ao inserir tabela: " . $e->getMessage() . " - <strong class='text-success'>ISSO NÃO AFETA A ATUALIZAÇÃO</strong>");
                    }
                }
            }
        }

        if(is_file("$raiz/temp-files/comand_api.sql")){
            $lines = file_get_contents("$raiz/temp-files/comand_api.sql");
            $lines = explode(";", $lines);
            foreach($lines as $sql){
                if(trim($sql)){
                    try{
                        \DB::unprepared("$sql;");
                        array_push($logMessage, "Comando SQL executado <strong class='text-info'>$sql;</strong>");

                    }catch(\Exception $e){
                        // array_push($logMessage, "Erro ao executar SQL: " . $e->getMessage() . " - <strong class='text-success'>ISSO NÃO AFETA A ATUALIZAÇÃO</strong>");
                    }
                }
            }
        }

        $dir = "$raiz/temp-files";
        $directories = array_diff(scandir($dir), array('..', '.'));

        foreach($directories as $dir){
            $source = "$raiz/temp-files/$dir";
            $map = $this->mapDiretories($dir);
            if($map != ""){
                $destiny = "$raiz$map";
                // echo $source . "<br>";
                array_push($logMessage, "Alteração de diretório <strong class='text-success'>$source</strong>");

                // echo $destiny . "<br>";
                shell_exec("cp -r $source $destiny");
            }
        }
        sleep(1);

        if(is_file("$raiz/temp-files/versao.txt")){
            $versao = file_get_contents("$raiz/temp-files/versao.txt");
            $versao = explode("=", $versao);
            $versao = isset($versao[1]) ? $versao[1] : "";
            $system = SystemUpdate::where('versao', $versao)->first();
            if($system == null){
                SystemUpdate::create(['versao' => $versao]);
            }
        }
        $this->unlinkr("$raiz/temp-files");
        session()->flash('mensagem_sucesso', "Sua aplicação foi atualizada!!");
        return view('update_file.finish', compact('logMessage'));
    }

    private function mapDiretories($dir){
        $mapsDiretories = [
            'app' => '/',
            'routes' => '/',
            'database' => '/',
            'resources' => '/',
            // 'vendor' => '/',
            'config' => '/',
            'js' => '/public',
            'css' => '/public',
            'imgs' => '/public',
            'delivery' => '/public',
            'food-files' => '/public',
        ];
        return isset($mapsDiretories[$dir]) ? $mapsDiretories[$dir] : "";
    }

    function unlinkr($dir)
    { 
        $files = array_diff(scandir($dir), array('.', '..')); 

        foreach ($files as $file) { 
            (is_dir("$dir/$file")) ? $this->unlinkr("$dir/$file") : unlink("$dir/$file"); 
        }

        return rmdir($dir); 
    } 

}
