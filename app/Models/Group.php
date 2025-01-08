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
        'links', // Ajoutez "links" si ce champ est autorisé dans votre table
        'created_by',
    ];

    protected $casts = [
        'links' => 'array', // Les liens seront castés en tableau PHP
    ];
    
    /**
     * Define the many-to-many relationship with users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }


    /*public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }*/

    /*public function requests()
    {
    return $this->hasMany(\App\Models\Request::class);
    }*/
}
