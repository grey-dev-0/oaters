<?php

namespace Modules\Common\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model{
    use HasFactory;

    /**
     * @inheritdoc
     */
    public $timestamps = false;

    /**
     * @inheritdoc
     */
    protected $table = 'lc_addresses';

    /**
     * @inheritdoc
     */
    protected $guarded = ['id'];

    /**
     * @inheritdoc
     */
    protected static function newFactory(){
        return \Modules\Common\Database\Factories\AddressesFactory::new();
    }

    public function contact(){
        return $this->belongsTo(Contact::class);
    }

    public function city(){
        return $this->belongsTo(City::class);
    }
}
