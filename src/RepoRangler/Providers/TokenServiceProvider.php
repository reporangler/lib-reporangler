<?php

namespace RepoRangler\Providers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use RepoRangler\Entity\AuthenticatedUser;
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
        Auth::viaRequest('api', function ($request) {
            /** @var AuthClient $authClient */
            $authClient = app(AuthClient::class);

            try{
                $response = $authClient->check($request->headers->get('Authorization'));
                $json = json_decode((string)$response->getBody(), true);
            }catch(ClientException $exception){
                return app(PublicUser::class);
            }

            return app(AuthenticatedUser::class, $json);
        });
    }
}
