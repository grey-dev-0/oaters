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

    public function applicant(){
        return $this->belongsTo(Applicant::class);
    }
}
