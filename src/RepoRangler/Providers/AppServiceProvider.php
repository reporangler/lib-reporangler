<?php

namespace RepoRangler\Providers;

use GuzzleHttp\Client;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;
use RepoRangler\Services\AuthClient;
use RepoRangler\Services\MetadataClient;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Validator::extend('required_xor', function($attr, $value, $params, $validator){
            $values = $validator->getData();
            $intersect = array_intersect_key(array_flip($params), $values);

            return empty($intersect);
        }, 'You can only provide :field when the :xor_fields are not provided');

        Validator::replacer('required_xor', function($message, $attr, $rule, $params){
            $message = str_replace(':field', $attr, $message);
            $message = str_replace(':xor_fields', implode(',', $params), $message);

            return $message;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(AuthClient::class, function(Application $app){
            $baseUrl = config('app.auth_base_url');

            $httpClient = app(Client::class);

            return new AuthClient($baseUrl, $httpClient);
        });

        $this->app->bind(MetadataClient::class, function(Application $app){
            $baseUrl = config('app.metadata_base_url');

            $httpClient = app(Client::class);

            $token = app('user-token');

            return new MetadataClient($baseUrl, $httpClient, $token);
        });

        $this->app->bindIf('user-token', function() {
            throw new AuthorizationException("You must override the 'user-token' service in your lumen app to provider the token from the user object");
        });
    }
}
