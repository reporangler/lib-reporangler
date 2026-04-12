<?php
namespace RepoRangler\Services;

use GuzzleHttp\Client;

class StorageClient
{
    private string $baseUrl;
    private string $publicUrl;
    private Client $httpClient;
    private ?string $token;

    public function __construct(string $baseUrl, string $publicUrl, Client $httpClient, ?string $token = null)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->publicUrl = rtrim($publicUrl, '/');
        $this->httpClient = $httpClient;
        $this->token = $token;
    }

    private function getHeaders(): array
    {
        $headers = ['Accept' => 'application/json'];
        if ($this->token) {
            $headers['Authorization'] = 'Bearer ' . $this->token;
        }
        return $headers;
    }

    public function upload(string $key, string $content): array
    {
        $response = $this->httpClient->put("$this->baseUrl/object/$key", [
            'headers' => array_merge($this->getHeaders(), [
                'Content-Type' => 'application/octet-stream',
            ]),
            'body' => $content,
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    public function download(string $key): ?string
    {
        try {
            $response = $this->httpClient->get("$this->baseUrl/object/$key", [
                'headers' => $this->getHeaders(),
            ]);

            return (string)$response->getBody();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function exists(string $key): bool
    {
        try {
            $response = $this->httpClient->get("$this->baseUrl/object-exists/$key", [
                'headers' => $this->getHeaders(),
            ]);

            $data = json_decode((string)$response->getBody(), true);
            return $data['exists'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function delete(string $key): bool
    {
        try {
            $response = $this->httpClient->delete("$this->baseUrl/object/$key", [
                'headers' => $this->getHeaders(),
            ]);

            $data = json_decode((string)$response->getBody(), true);
            return $data['deleted'] ?? false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getDownloadUrl(string $key): string
    {
        return "$this->publicUrl/object/$key";
    }

    public function getInternalDownloadUrl(string $key): string
    {
        return "$this->baseUrl/object/$key";
    }
}
