<?php
namespace RepoRangler\Entity;

use Illuminate\Database\Eloquent\Model;

class RepositoryType extends Model
{
    const PATTERN = '[a-z][a-z0-9\-\.]+';

    protected $fillable = ['id', 'name'];
}
