<?php
namespace RepoRangler\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use RepoRangler\Entity\PackageGroup;

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

	public function __construct(string $baseUrl, Client $httpClient)
	{
		$this->baseUrl = $baseUrl;
		$this->httpClient = $httpClient;
	}

	public function getPackages(string $token, string $repositoryType): array
	{
		$response = $this->httpClient->get("$this->baseUrl/packages/$repositoryType", [
			'headers' => [
				'Authorization' => 'Bearer ' . $token,
				'Accept' => 'application/json',
			],
		]);

		return json_decode((string)$response->getBody(), true);
	}

	public function addPackage(string $token, string $repositoryType, string $packageGroup, string $packageName, string $packageVersion, array $definition): array
	{
		$response = $this->httpClient->post("$this->baseUrl/packages/$repositoryType", [
			'headers' => [
				'Authorization' => 'Bearer ' . $token,
				'Accept' => 'application/json',
			],
			RequestOptions::JSON => [
				'name' => $packageName,
				'version' => $packageVersion,
				'definition' => $definition,
				'package_group' => $packageGroup,
			]
		]);

		return json_decode((string)$response->getBody(), true);
	}

	public function getPackageGroupById(string $token, int $id): PackageGroup
	{
		$response = $this->httpClient->get("$this->baseUrl/package-group/$id", [
			'headers' => [
				'Authorization' => 'Bearer ' . $token,
				'Accept' => 'application/json',
			],
		]);

		return new PackageGroup(json_decode((string)$response->getBody(), true));
	}
}
