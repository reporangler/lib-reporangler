<?php
namespace RepoRangler\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use RepoRangler\Entity\PackageGroup;
use RepoRangler\Entity\Repository;

class MetadataClient
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
	private $token;

	private function getHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->token,
            'Accept' => 'application/json',
        ];
    }

	public function __construct(string $baseUrl, Client $httpClient, string $token)
	{
		$this->baseUrl = $baseUrl;
		$this->httpClient = $httpClient;
		$this->token = $token;
	}

	public function getPackages(string $repositoryType): array
	{
		$response = $this->httpClient->get("$this->baseUrl/packages/$repositoryType", [
			'headers' => $this->getHeaders(),
		]);

		return json_decode((string)$response->getBody(), true);
	}

	public function addPackage(string $repositoryType, string $packageGroup, string $packageName, string $packageVersion, array $definition): array
	{
		$response = $this->httpClient->post("$this->baseUrl/packages/$repositoryType", [
            'headers' => $this->getHeaders(),
			RequestOptions::JSON => [
				'name' => $packageName,
				'version' => $packageVersion,
				'definition' => $definition,
				'package_group' => $packageGroup,
			]
		]);

		return json_decode((string)$response->getBody(), true);
	}

	public function getPackageGroupById(int $id): PackageGroup
	{
		$response = $this->httpClient->get("$this->baseUrl/package-group/$id", [
            'headers' => $this->getHeaders(),
		]);

		return new PackageGroup(json_decode((string)$response->getBody(), true));
	}

    public function getPackageGroupByName(string $name): PackageGroup
    {
        $response = $this->httpClient->get("$this->baseUrl/package-group/$name", [
            'headers' => $this->getHeaders(),
        ]);

        return new PackageGroup(json_decode((string)$response->getBody(), true));
    }

    public function getRepositoryList(): Collection
    {
        $response = $this->httpClient->get("$this->baseUrl/repository", [
            'headers' => $this->getHeaders(),
        ]);

        return collect(json_decode((string)$response->getBody(), true));
    }

	public function getRepositoryById(int $id): Repository
    {
        $response = $this->httpClient->get("$this->baseUrl/repository/$id", [
            'headers' => $this->getHeaders(),
        ]);

        return new Repository(json_decode((string)$response->getBody(), true));
    }

    public function getRepositoryByName(string $name): Repository
    {
        $response = $this->httpClient->get("$this->baseUrl/repository/$name", [
            'headers' => $this->getHeaders(),
        ]);

        return new Repository(json_decode((string)$response->getBody(), true));
    }
}
