<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function showFile($path)
    {
        $ext = explode(".", $path);
        $ext = end($ext);
        $ext = strtolower($ext);

        if($ext == 'pdf'){
            $mime_type = 'application/pdf';
        }else{
            $mime_type = 'image/'.$ext;
        }

        if( isset($path) ){
            $fileUrl = storage_path(). '/app/uploads/'.$path;
            return response()->file($fileUrl, array('Content-Type' => $mime_type));
        }else{
            return abort('403');
        }
    }

}
