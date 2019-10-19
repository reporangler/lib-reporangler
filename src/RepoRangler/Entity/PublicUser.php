<?php
namespace RepoRangler\Entity;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Collection;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class PublicUser extends User implements UserInterface, AuthorizableContract
{
    public function __construct(array $attributes = [])
    {
        $defaultAttributes = [
            'username' => UserInterface::PUBLIC_USERNAME,
            'email' => null,
            'token' => UserInterface::PUBLIC_TOKEN,
            'capability' => new Collection([
                new CapabilityMap([
                    'name' => Capability::IS_PUBLIC_USER,
                ]),
                new CapabilityMap([
                    'name' => Capability::REPOSITORY_ACCESS,
                    'constraint' => ['name' => 'php']
                ]),
                new CapabilityMap([
                    'name' => Capability::PACKAGE_GROUP_ACCESS,
                    'constraint' => ['name' => 'public']
                ]),
            ]),
        ];

        $attributes = array_merge($defaultAttributes, $attributes);

        return parent::__construct($attributes);
    }
}
