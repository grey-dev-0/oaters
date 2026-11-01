<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Common\App\Models\Contact;

class Punch extends Model
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $table = 'r_punches';

    /**
     * r_punches tracks creation time only.
     *
     * @var string
     */
    const UPDATED_AT = null;

    /**
     * {@inheritdoc}
     */
    protected $guarded = ['id'];

    protected static function newFactory()
    {
        return \Modules\Ruby\Database\Factories\PunchesFactory::new();
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
