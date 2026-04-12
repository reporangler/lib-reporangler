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

    /**
     * @var string|null
     */
    private $token = null;

    public function __construct(string $baseUrl, Client $httpClient)
    {
        $this->baseUrl = $baseUrl;
        $this->httpClient = $httpClient;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    private function getAuthHeaders(): array
    {
        $headers = ['Accept' => 'application/json'];
        if ($this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }
        return $headers;
    }

    private function getRequestData(?array $fields = null): array
    {
        $data = ['headers' => $this->getAuthHeaders()];
        if (!empty($fields)) {
            $data[RequestOptions::JSON] = $fields;
        }
        return $data;
    }

    public function login(string $type, string $username, string $password): array
    {
        $response = $this->httpClient->get($this->baseUrl.'/login/api', [
            'headers' => [
                'reporangler-login-type' => $type,
                'reporangler-login-username' => $username,
                'reporangler-login-password' => $password,
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    public function check(string $token): array
    {
        // Just in case the string is direct from the header of a subrequest, strip this out
        $token = str_replace('Bearer','',$token);
        $token = trim($token);

        if($token === PublicUser::PUBLIC_TOKEN){
            throw new \InvalidArgumentException("Cannot check public tokens");
        }

        $response = $this->httpClient->get($this->baseUrl . '/login/token', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    public function getUsers(): array
    {
        $response = $this->httpClient->get($this->baseUrl . '/user', $this->getRequestData());
        return json_decode((string)$response->getBody(), true);
    }

    public function getUserById(int $id): array
    {
        $response = $this->httpClient->get($this->baseUrl . '/user/' . $id, $this->getRequestData());
        return json_decode((string)$response->getBody(), true);
    }

    public function getUserByUsername(string $username): array
    {
        $response = $this->httpClient->get($this->baseUrl . '/user/' . $username, $this->getRequestData());
        return json_decode((string)$response->getBody(), true);
    }

    public function createUser(string $username, string $email, string $password): array
    {
        $response = $this->httpClient->post($this->baseUrl . '/user', $this->getRequestData([
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ]));
        return json_decode((string)$response->getBody(), true);
    }

    public function updateUser(int $id, array $fields): array
    {
        $response = $this->httpClient->put($this->baseUrl . '/user/' . $id, $this->getRequestData($fields));
        return json_decode((string)$response->getBody(), true);
    }

    public function deleteUser(int $id): array
    {
        $response = $this->httpClient->delete($this->baseUrl . '/user/' . $id, $this->getRequestData());
        return json_decode((string)$response->getBody(), true);
    }

    public function getAccessTokens(int $userId): array
    {
        $response = $this->httpClient->get($this->baseUrl . '/access-token/' . $userId, $this->getRequestData());
        return json_decode((string)$response->getBody(), true);
    }

    public function addAccessToken(int $userId, string $type, string $token): array
    {
        $response = $this->httpClient->post($this->baseUrl . '/access-token/' . $userId, $this->getRequestData([
            'type' => $type,
            'token' => $token,
        ]));
        return json_decode((string)$response->getBody(), true);
    }

    public function removeAccessToken(int $userId, int $tokenId): array
    {
        $response = $this->httpClient->delete($this->baseUrl . '/access-token/' . $userId . '/' . $tokenId, $this->getRequestData());
        return json_decode((string)$response->getBody(), true);
    }

    public function giveAdmin(int $userId): array
    {
        $response = $this->httpClient->put($this->baseUrl . '/permission/user/admin/' . $userId, $this->getRequestData());
        return json_decode((string)$response->getBody(), true);
    }

    public function removeAdmin(int $userId): array
    {
        $response = $this->httpClient->delete($this->baseUrl . '/permission/user/admin/' . $userId, $this->getRequestData());
        return json_decode((string)$response->getBody(), true);
    }

    public function joinPackageGroup(array $data): array
    {
        $response = $this->httpClient->post($this->baseUrl . '/permission/package-group/join', $this->getRequestData($data));
        return json_decode((string)$response->getBody(), true);
    }

    public function leavePackageGroup(array $data): array
    {
        $response = $this->httpClient->post($this->baseUrl . '/permission/package-group/leave', $this->getRequestData($data));
        return json_decode((string)$response->getBody(), true);
    }

    public function protectPackageGroup(array $data): array
    {
        $response = $this->httpClient->post($this->baseUrl . '/permission/package-group/protect', $this->getRequestData($data));
        return json_decode((string)$response->getBody(), true);
    }

    public function unprotectPackageGroup(array $data): array
    {
        $response = $this->httpClient->post($this->baseUrl . '/permission/package-group/unprotect', $this->getRequestData($data));
        return json_decode((string)$response->getBody(), true);
    }
}
