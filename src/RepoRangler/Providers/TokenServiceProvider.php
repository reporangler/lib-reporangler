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
                $token = $request->headers->get('Authorization');
                $response = $authClient->check($token);
                $json = json_decode((string)$response->getBody(), true);
                return new AuthenticatedUser($json);
            }catch(ClientException $exception){
                $repositoryType = $request->headers->get('reporangler-repository-type');
                return new PublicUser($repositoryType);
            }
        });
    }
}
