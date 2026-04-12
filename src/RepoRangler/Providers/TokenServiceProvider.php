<?php

namespace RepoRangler\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use RepoRangler\Entity\User;
use RepoRangler\Services\AuthClient;

class TokenServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Auth::viaRequest('token', function (Request $request) {
            $token = $request->header('authorization');

            if (empty($token)) {
                return null;
            }

            try {
                $authClient = app(AuthClient::class);
                $response = $authClient->check($token);
                return new User($response);
            } catch (\Exception $e) {
                return null;
            }
        });
    }
}
