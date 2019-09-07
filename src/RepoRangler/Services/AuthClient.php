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

    public function __construct(string $baseUrl, Client $httpClient)
    {
        $this->baseUrl = $baseUrl;
        $this->httpClient = $httpClient;
    }

    public function login(string $type, string $username, string $password, string $repositoryType): ResponseInterface
    {
        return $this->httpClient->post($this->baseUrl.'/user/login', [
            RequestOptions::JSON => [
                'type' => $type,
                'username' => $username,
                'password' => $password,
                'repository_type' => $repositoryType,
            ]
        ]);
    }

    public function check(string $token): ResponseInterface
    {
        // Just in case the string is direct from the header of a subrequest, strip this out
        $token = str_replace('Bearer','',$token);
        $token = trim($token);

        return $this->httpClient->post($this->baseUrl . '/user/check', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    }
}
