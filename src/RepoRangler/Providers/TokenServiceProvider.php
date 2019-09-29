<?php

namespace RepoRangler\Providers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use RepoRangler\Entity\RepositoryUser;
use RepoRangler\Entity\PublicUser;
use RepoRangler\Services\AuthClient;

class TokenServiceProvider extends ServiceProvider
{
    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        Auth::viaRequest('token', function (Request $request) {
            $authClient = app(AuthClient::class);

            $response = $authClient->checkToken($request->header('authorization'));

            return new RestUser($response);
        });
    }
}
