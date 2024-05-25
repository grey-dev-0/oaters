<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model{
    use HasFactory;

    /**
     * @inheritdoc
     */
    protected $table = 'r_documents';

    /**
     * @inheritdoc
     */
    protected $guarded = [];

    /**
     * @inheritdoc
     */
    protected static function newFactory(){
        return \Modules\Ruby\Database\Factories\DocumentsFactory::new();
    }

    public function applicant(){
        return $this->belongsTo(Applicant::class);
    }

    public function getFilenameAttribute($filename){
        return \Storage::disk('local')->path("r_documents/$filename");
    }
}
