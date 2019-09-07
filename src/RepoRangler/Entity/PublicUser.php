<?php
namespace RepoRangler\Entity;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class PublicUser extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    const PUBLIC_TOKEN = 'public';

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
    protected $hidden = [
        'password',
    ];

    public function __construct(string $repositoryType)
    {
        parent::__construct([
            'id' => 0,
            'username' => 'public-user',
            'token' => self::PUBLIC_TOKEN,
            'repository_type' => $repositoryType,
            'package_groups' => [
                [
                    'id' => 0,
                    'name' => 'public'
                ]
            ],
        ]);
    }
}
