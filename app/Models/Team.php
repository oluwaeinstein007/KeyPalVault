<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'team_user', 'team_id', 'user_id');
    }

    // public function addLeader(User $user)
    // {
    //     $this->members()->attach($user->id, ['is_leader' => true]);
    // }

    // public function removeLeader(User $user)
    // {
    //     $this->members()->attach($user->id, ['is_leader' => false]);
    // }
    public function addLeader(User $user)
    {
        $this->members()->sync([$user->id => ['is_leader' => true]], false);
    }

    public function removeLeader(User $user)
    {
        $this->members()->sync([$user->id => ['is_leader' => false]], false);
    }

    public function addUser(User $user)
    {
        $this->members()->attach($user->id, ['is_leader' => false]);
    }

    public function removeUser(User $user)
    {
        $this->members()->detach($user->id);
    }

    public function hasUser(User $user)
    {
        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function vaults()
    {
        return $this->belongsToMany(Vault::class);
    }
}
