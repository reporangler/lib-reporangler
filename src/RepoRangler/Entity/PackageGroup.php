<?php
namespace RepoRangler\Entity;

use Illuminate\Database\Eloquent\Model;

class PackageGroup extends Model
{
    const PUBLIC_GROUP = 'public';
    const PATTERN = '[a-z][a-z0-9\-\.]+';

    protected $fillable = ['id', 'name'];
}
