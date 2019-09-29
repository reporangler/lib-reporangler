<?php
namespace RepoRangler\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use RepoRangler\Entity\PublicUser;

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
        return $this->httpClient->post($this->baseUrl.'/login/api', [
            'headers' => [
                'reporangler-login-type' => $type,
                'reporangler-login-username' => $username,
                'reporangler-login-password' => $password,
            ],
        ]);
    }

    public function check(string $token): ResponseInterface
    {
        // Just in case the string is direct from the header of a subrequest, strip this out
        $token = str_replace('Bearer','',$token);
        $token = trim($token);

        if($token === PublicUser::PUBLIC_TOKEN){
            throw new \InvalidArgumentException("Cannot check public tokens");
        }

        return $this->httpClient->get($this->baseUrl . '/login/token', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);
    }
}
