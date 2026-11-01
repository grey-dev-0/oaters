<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Common\App\Models\Contact;

class Subordinate extends Model
{
    /**
     * {@inheritdoc}
     */
    public $incrementing = false;

    /**
     * {@inheritdoc}
     */
    protected $table = 'r_subordinates';

    /**
     * r_subordinates carries no timestamp columns.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * {@inheritdoc}
     */
    protected $guarded = ['id'];

    /**
     * Get the manager contact
     */
    public function manager()
    {
        return $this->belongsTo(Contact::class, 'manager_id');
    }

    /**
     * Get the contact
     */
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the department
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
