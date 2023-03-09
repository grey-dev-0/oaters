<?php

namespace Modules\Ruby\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Document extends Model{
    use HasFactory, UsesTenantDatabase;

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
