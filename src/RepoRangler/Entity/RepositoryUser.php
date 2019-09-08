<?php
namespace RepoRangler\Entity;

use Illuminate\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class RepositoryUser extends Model implements RepoRanglerUserInterface, AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    public $id;
    public $username;
    public $token;
    public $repository_type;
    public $package_groups = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'username', 'token', 'repository_type', 'package_groups'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password'];

    public function __construct(array $attributes)
    {
        foreach($this->fillable as $key){
            if(array_key_exists($key, $attributes)){
                $this->$key = $attributes[$key];
            }else if($key !== 'token'){
                // Token can be missing, say if the user is being checked and not logged in
                throw new \InvalidArgumentException("Attributes array is missing key '$key'");
            }
        }
    }
}
