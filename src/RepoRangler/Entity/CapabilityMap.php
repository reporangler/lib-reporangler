<?php
namespace RepoRangler\Entity;

use Illuminate\Database\Eloquent\Model;

class CapabilityMap extends Model
{
    protected $fillable = ['entity_type', 'entity_id', 'capability_id', 'name', 'constraint'];
}
