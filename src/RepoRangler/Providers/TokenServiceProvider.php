<?php

namespace RepoRangler\Providers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Http\Request;
use RepoRangler\Entity\User;
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

            $response = $authClient->check($request->header('authorization'));

            return new User($response);
        });
    }
}
