<?php
namespace RepoRangler\Entity;

use Illuminate\Database\Eloquent\Model;

class Capability extends Model
{
    const IS_PUBLIC_USER        = 'IS_PUBLIC_USER';
    const IS_ADMIN_USER         = 'IS_ADMIN_USER';
    const IS_REST_USER          = 'IS_REST_USER';
    const IS_REPO_USER          = 'IS_REPO_USER';

    const REPOSITORY_ADMIN      = 'REPOSITORY_ADMIN';
    const REPOSITORY_ACCESS     = 'REPOSITORY_ACCESS';

    const PACKAGE_GROUP_ADMIN   = 'PACKAGE_GROUP_ADMIN';
    const PACKAGE_GROUP_ACCESS  = 'PACKAGE_GROUP_ACCESS';
}
