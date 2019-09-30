<?php
namespace RepoRangler\Entity;

use Illuminate\Database\Eloquent\Model;

class UserCapability extends Model
{
    protected $fillable = ['user_id', 'capability_id', 'name', 'constraint'];
}
