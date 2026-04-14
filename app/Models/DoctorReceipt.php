<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DoctorReceipt extends Model
{
    use HasFactory;

    protected $table = 'doctor_receipts';

    protected $fillable = [
        'receipt',
        'receipt_url',
        'receipt_no',
        'created_by',
        'is_active',
    ];

    protected $casts = [
        'created_by' => 'integer',
        'is_active' => 'boolean',
    ];
}
