<?php
namespace RepoRangler\Entity;

use Illuminate\Database\Eloquent\Model;

class PackageGroup extends Model
{
    const PUBLIC_GROUP = 'public';

    protected $fillable = ['id', 'name'];
}
