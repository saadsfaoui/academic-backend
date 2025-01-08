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
        'links', // Les liens sont inclus dans les champs autorisés
        'created_by',
    ];

    protected $casts = [
        'links' => 'array', // Les liens seront castés en tableau PHP
    ];

    /**
     * Relation avec l'utilisateur créateur.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relation "many-to-many" avec les utilisateurs.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }

    /**
     * Relation avec les requêtes associées au groupe.
     */
    public function requests()
    {
        return $this->hasMany(\App\Models\Request::class);
    }
}
