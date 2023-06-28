<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vault extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'content',
        'note',
        'visibility',
        'user_id'
    ];

    protected $casts = [
        'content' => 'array',
    ];

    public function addTeam(Team $team)
    {
        $this->teams()->attach($team->id);
    }

    public function removeTeam(Team $team)
    {
        $this->teams()->detach($team->id);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }

    public function hasTeam(Team $team)
    {
        return $this->teams()->where('team_id', $team->id)->exists();
    }
}
