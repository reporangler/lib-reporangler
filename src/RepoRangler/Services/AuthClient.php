<?php
namespace RepoRangler\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

class AuthClient
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var string
     */
    private $repositoryType;

    public function __construct(string $baseUrl, Client $httpClient, string $repositoryType)
    {
        $this->baseUrl = $baseUrl;
        $this->httpClient = $httpClient;
        $this->repositoryType = $repositoryType;
    }

    public function login($type, $username, $password): ResponseInterface
    {
        return $this->httpClient->post($this->baseUrl.'/user/login', [
            RequestOptions::JSON => [
                'type' => $type,
                'username' => $username,
                'password' => $password,
                'repository_type' => $this->repositoryType,
            ]
        ]);
    }
}
