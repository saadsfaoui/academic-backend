<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'score',
        'date',
        'user_id', // Ajoutez ce champ
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

