<?php

namespace RepoRangler\Providers;

use GuzzleHttp\Client;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use RepoRangler\Entity\PublicUser;
use RepoRangler\Entity\AuthenticatedUser;
use RepoRangler\Services\AuthClient;
use RepoRangler\Services\MetadataClient;

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

        $this->app->bind(AuthenticatedUser::class, function(Application $app, array $params){
            return new AuthenticatedUser($params);
        });

        $this->app->bind(AuthClient::class, function(Application $app){
            $baseUrl = config('app.auth_base_url');
            $repositoryType = config('app.repository_type');

            $httpClient = app(Client::class);

            return new AuthClient($baseUrl, $httpClient, $repositoryType);
        });

        $this->app->bind(MetadataClient::class, function(Application $app){
            $baseUrl = config('app.metadata_base_url');

            $httpClient = app(Client::class);

            return new MetadataClient($baseUrl, $httpClient);
        });
    }
}
