<?php

namespace Modules\Ruby\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollPayment extends Model
{
    use HasFactory;

    /**
     * {@inheritdoc}
     */
    protected $table = 'r_payroll_payments';

    protected $fillable = [];

    protected static function newFactory()
    {
        return \Modules\Ruby\Database\factories\PayrollPaymentFactory::new();
    }
}
