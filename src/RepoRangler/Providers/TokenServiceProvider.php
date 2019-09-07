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

            $token = $request->headers->get('Authorization');

            try{
                $response = $authClient->check($token);
                if($response->getStatusCode() === 200){
                    $json = json_decode((string)$response->getBody(), true);
                    return new AuthenticatedUser($json);
                }
            }catch(Exception $exception){
                error_log('Exception: '.$exception->getMessage());
                /* Catch errors, but don't do anthing about them */
            }

            $repositoryType = $request->headers->get('reporangler-repository-type');
            return new PublicUser($repositoryType);
        });
    }
}
