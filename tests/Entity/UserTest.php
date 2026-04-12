<?php

use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\TestCase;
use RepoRangler\Entity\Capability;
use RepoRangler\Entity\CapabilityMap;
use RepoRangler\Entity\User;

class UserTest extends TestCase
{
    /**
     * Create a User with the given capability array set directly on attributes
     * to bypass the recursive mutator.
     */
    private function createUserWithCapabilities(array $capabilities): User
    {
        $user = new User();
        $user->username = 'testuser';

        $reflection = new \ReflectionProperty(Model::class, 'attributes');
        $reflection->setAccessible(true);
        $attrs = $reflection->getValue($user);
        $attrs['capability'] = $capabilities;
        $reflection->setValue($user, $attrs);

        return $user;
    }

    public function testHasCapabilityReturnsTrueWhenPresent()
    {
        $cap = new CapabilityMap(['name' => Capability::IS_ADMIN_USER]);
        $user = $this->createUserWithCapabilities([$cap]);

        $this->assertTrue($user->hasCapability(Capability::IS_ADMIN_USER));
    }

    public function testHasCapabilityReturnsFalseWhenAbsent()
    {
        $user = $this->createUserWithCapabilities([]);

        $this->assertFalse($user->hasCapability(Capability::IS_ADMIN_USER));
    }

    public function testIsAdminUserAttribute()
    {
        $cap = new CapabilityMap(['name' => Capability::IS_ADMIN_USER]);
        $user = $this->createUserWithCapabilities([$cap]);

        $this->assertTrue($user->is_admin_user);
    }

    public function testIsAdminUserAttributeFalseWhenNotAdmin()
    {
        $cap = new CapabilityMap(['name' => Capability::IS_REST_USER]);
        $user = $this->createUserWithCapabilities([$cap]);

        $this->assertFalse($user->is_admin_user);
    }

    public function testGetPackageGroupsAttribute()
    {
        $cap = new CapabilityMap([
            'name' => Capability::PACKAGE_GROUP_ACCESS,
            'constraint' => ['package_group' => 'public', 'admin' => false],
        ]);
        $user = $this->createUserWithCapabilities([$cap]);

        $packageGroups = $user->package_groups;

        $this->assertSame(['public' => 'access'], $packageGroups);
    }
}
