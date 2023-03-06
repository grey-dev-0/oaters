<?php

namespace Modules\Ruby\Entities;

use App\Traits\UsesTenantDatabase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Applicant extends Model{
    use HasFactory, UsesTenantDatabase;

    /**
     * @inheritdoc
     */
    protected $table = 'r_applicants';
}
