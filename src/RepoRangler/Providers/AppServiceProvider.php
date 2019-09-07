<?php

namespace RepoRangler\Providers;

use App\Services\MetadataClient;
use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use RepoRangler\Entity\PublicUser;
use RepoRangler\Services\AuthClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Client::class);

        $this->app->bind(PublicUser::class, function(Application $app){
            $repositoryType = config('app.repository_type');

            return new PublicUser($repositoryType);
        });

        $this->app->bind(PublicUser::class, function(Application $app, array $params){
            return new AuthenticatedUser($params);
        });

        $this->app->bind(AuthClient::class, function(Application $app){
            $baseUrl = config('app.auth_base_url');
            $repositoryType = config('app.repository_type');

            $httpClient = $app->make(Client::class);

            return new AuthClient($baseUrl, $httpClient, $repositoryType);
        });

        $this->app->bind(MetadataClient::class, function(Application $app){
            $baseUrl = config('app.metadata_base_url');

            $httpClient = $app->make(Client::class);

            return new MetadataClient($baseUrl, $httpClient);
        });
    }
}
