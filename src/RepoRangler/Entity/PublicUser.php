<?php
namespace RepoRangler\Entity;

use Illuminate\Database\Eloquent\Model;

class PublicUser extends Model implements UserInterface
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id', 'username', 'token', 'capability_map', 'package_groups'];

    protected $appends = ['is_admin_user', 'is_rest_user', 'package_groups'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password'];

    public function __construct(array $attributes = [])
    {
        parent::__construct([
            'id' => 0,
            'username' => UserInterface::PUBLIC_USERNAME,
            'token' => UserInterface::PUBLIC_TOKEN,
            'capability_map' => [
                new UserCapability(['name' => UserCapability::IS_PUBLIC_USER]),
            ],
            'package_groups' => [
                new PackageGroup(['name' => PackageGroup::PUBLIC_GROUP]),
            ],
        ]);
    }

    public function setUsername(string $username): UserInterface
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

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
        return $this->hasCapability(UserCapability::IS_PUBLIC_USER);
    }

    public function getIsAdminUserAttribute(): bool
    {
        return $this->hasCapability(UserCapability::IS_ADMIN_USER);
    }

    public function getIsRestUserAttribute(): bool
    {
        return $this->hasCapability(UserCapability::IS_REST_USER);
    }

    public function getIsRepoUserAttribute(): bool
    {
        return $this->hasCapability(UserCapability::IS_REPO_USER);
    }
}
