<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Monitoring extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
		'request' => 'array',
		'regions' => 'array',
		'payload' => 'array',
	];
}
