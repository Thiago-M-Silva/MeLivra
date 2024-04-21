<?php

namespace App\Http\Controllers;

use App\Http\Helpers\BuscarProfessores;
use App\Http\Helpers\StorageS3;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class BuscarProfessorController extends Controller{

    public function showFormBuscarProfessor(Request $request){
        $buscarProfessor=new BuscarProfessores();
        $dados = $buscarProfessor->Buscar();
        return Inertia::render('BuscarProfessor',["profs"=>$dados]);
    }


    public function BucarInformacaoProfessor(Request $request){
        $dados = $request->all();
        $validar = Validator::make($request->all(),

        [
            'nomeProfessor'=> 'required|string',
            'instituto' => 'required|string',
        ]);
 
        if($validar->fails()){
         return "erro , corpo invalido";
        }

        $buscarProfessor= new BuscarProfessores();
        $comentarios = $buscarProfessor->BuscarComentarios($dados['nomeProfessor'],$dados['instituto']);
        $notas = $buscarProfessor->BuscarNota($dados['nomeProfessor'],$dados['instituto']);
        $ids = [];
        foreach ($comentarios as $coment){
            $id = $coment->criadoBY;
            array_push($ids,$id);
        }
        $inst = strtolower($dados['instituto']);
        $s3 = new StorageS3();
        $file_url2022_2= $s3->getUrl($inst,$dados['nomeProfessor'],"2022_2_v.png");
        $file_url2023_1= $s3->getUrl($inst,$dados['nomeProfessor'],"2023_1_v.png");

         
        $user = $buscarProfessor->BuscarUserQueFezComentarioPorID($ids);

        return Inertia::render('InfoDoProfessor',[
            'comentarios'=>$comentarios,
            'notas'=>$notas,
            'user'=>$user,
            'nomeProfessor'=>$dados['nomeProfessor'],
            'instituto'=>strtolower($dados['instituto']),
            'file_url2022_2'=>$file_url2022_2,
            'file_url2023_1'=>$file_url2023_1,
        ]);



    }
}