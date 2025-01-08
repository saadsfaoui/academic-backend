<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'links',
        'created_by',
    ];

    protected $casts = [
        'links' => 'array', // Les liens sont castés en tableau PHP
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
