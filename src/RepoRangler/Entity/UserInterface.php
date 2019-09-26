<?php
namespace RepoRangler\Entity;

interface UserInterface
{
    const PUBLIC_USERNAME = 'public-user';
    const PUBLIC_TOKEN = 'public';

    public function setUsername(string $username): UserInterface;
    public function getUsername(): string;

    public function hasCapability($name, $constraint = null): bool;
    public function getCapability($name, $constraint = null): ?UserCapability;

    public function getPackageGroupsAttribute(): array;

    public function getIsPublicUserAttribute(): bool;
    public function getIsAdminUserAttribute(): bool;
    public function getIsRestUserAttribute(): bool;
    public function getIsRepoUserAttribute(): bool;
}
