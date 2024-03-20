<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatusSetting extends Model
{
    use HasFactory;

    protected $table = 'status_setting';

    protected $fillable = ['current_status', 'next_status',];
}
