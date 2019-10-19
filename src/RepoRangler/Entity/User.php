<?php
namespace RepoRangler\Entity;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Collection;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements UserInterface, AuthorizableContract
{
    use Authorizable;

    const PATTERN = '[a-z][a-z0-9\-\.]+';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username',
        'email',
        'token',
        'password',
        'capability',
    ];

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

    public function getCapability($name, $constraint = null): ?CapabilityMap
    {
        foreach($this->capability as $cap){
            if($cap->name === $name){
                return $cap;
            }
        }

        return null;
    }

    public function setCapabilityAttribute(array $list)
    {
        $this->capability = array_map(function ($item){
            if($item instanceof CapabilityMap) return $item;

            return new CapabilityMap((array)$item);
        }, $list);
    }

    public function getPackageGroupsAttribute(): array
    {
        $packageGroups = [];

        foreach($this->capability as $cap){
            if(in_array($cap->name, [Capability::PACKAGE_GROUP_ACCESS, Capability::PACKAGE_GROUP_ADMIN])){
                $packageGroups[$cap->constraint['package_group']] = $cap->name;
            }
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
