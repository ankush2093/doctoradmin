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
        'is_active'
    ];
}