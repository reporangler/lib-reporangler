<?php
namespace RepoRangler\Entity;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Collection;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements UserInterface, AuthorizableContract
{
    use Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['username', 'email', 'password'];

    protected $appends = ['is_admin_user', 'is_rest_user', 'package_groups'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password'];

    public function hasCapability($name, $constraint = null): bool
    {
        return !!$this->getCapability($name, $constraint);
    }

    public function getCapability($name, $constraint = null): ?UserCapability
    {
        foreach($this->capability as $cap){
            if($cap->name === $name){
                return $cap;
            }
        }

        return null;
    }

    public function getPackageGroupsAttribute(): array
    {
        $packageGroups = [];

        foreach($this->capability as $cap) {

        }

        return $packageGroups;
    }

    public function getIsPublicUserAttribute(): bool
    {
        return $this->hasCapability(Capability::IS_PUBLIC_USER);
    }

    public function getIsAdminUserAttribute(): bool
    {
        return $this->hasCapability(Capability::IS_ADMIN_USER);
    }

    public function getIsRestUserAttribute(): bool
    {
        return $this->hasCapability(Capability::IS_REST_USER);
    }

    public function getIsRepoUserAttribute(): bool
    {
        return $this->hasCapability(Capability::IS_REPO_USER);
    }
}
