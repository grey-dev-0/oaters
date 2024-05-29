<?php

namespace Modules\Ruby\App\Http\Controllers;

use App\Http\Controllers\Controller;

class DocumentController extends Controller{
    public function getDocument($filename){
        return \Storage::disk('local')->download("r_documents/$filename");
    }
}