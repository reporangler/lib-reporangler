<?php
namespace RepoRangler\Services;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use Psr\Http\Message\ResponseInterface;
use RepoRangler\Entity\PackageGroup;
use RepoRangler\Entity\Repository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

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

    private function getRequestData(?array $fields = null): array
    {
        $data = ['headers' => $this->getHeaders()];

        if(!empty($fields)){
            $data[RequestOptions::JSON] = $fields;
        }

        return $data;
    }

    private function decode(ResponseInterface $response)
    {
        return json_decode((string)$response->getBody(), true);
    }

    private function decodeList(ResponseInterface $response, string $type)
    {
        $response = $this->decode($response);

        if(array_key_exists('data', $response)){
            $response['data'] = collect($response['data'])->map(function($value) use ($type) {
                return new $type($value);
            });

            return $response;
        }

        throw new UnprocessableEntityHttpException('This endpoint did not return a list as expected');
    }

	public function __construct(string $baseUrl, Client $httpClient, ?string $token = null)
	{
		$this->baseUrl = $baseUrl;
		$this->httpClient = $httpClient;
		$this->token = $token;
	}

	public function getPackages(string $repositoryType): array
	{
		$response = $this->httpClient->get("$this->baseUrl/package/$repositoryType", $this->getRequestData());

		return json_decode((string)$response->getBody(), true);
	}

	public function addPackage(string $repositoryType, string $packageGroup, string $packageName, string $packageVersion, array $definition, ?string $storageKey = null, ?string $packageType = null): array
	{
	    $data = [
            'name' => $packageName,
            'version' => $packageVersion,
            'definition' => $definition,
            'package_group' => $packageGroup,
        ];

        if ($storageKey !== null) {
            $data['storage_key'] = $storageKey;
        }
        if ($packageType !== null) {
            $data['package_type'] = $packageType;
        }

		$response = $this->httpClient->post("$this->baseUrl/package/$repositoryType", $this->getRequestData($data));

		return json_decode((string)$response->getBody(), true);
	}

	public function getPackagesByName(string $repositoryType, string $packageName): array
	{
		$response = $this->httpClient->get("$this->baseUrl/package/$repositoryType/by-name/" . urlencode($packageName), $this->getRequestData());

		return json_decode((string)$response->getBody(), true);
	}

	public function createPackageGroup(string $name): PackageGroup
    {
        $response = $this->httpClient->post("$this->baseUrl/package-group", $this->getRequestData([
            'name' => $name,
        ]));

        return new PackageGroup($this->decode($response));
    }

    public function getPackageGroupList(): array
    {
        $response = $this->httpClient->get("$this->baseUrl/package-group", $this->getRequestData());

        return $this->decodeList($response, PackageGroup::class);
    }

	public function getPackageGroupById(int $id): PackageGroup
	{
		$response = $this->httpClient->get("$this->baseUrl/package-group/$id", $this->getRequestData());

		return new PackageGroup($this->decode($response));
	}

    public function getPackageGroupByName(string $name): PackageGroup
    {
        $response = $this->httpClient->get("$this->baseUrl/package-group/$name", $this->getRequestData());

        return new PackageGroup($this->decode($response));
    }

    public function createRepository(string $name): Repository
    {
        $response = $this->httpClient->post("$this->baseUrl/repository", $this->getRequestData([
            'name' => $name,
        ]));

        return new Repository($this->decode($response));
    }

    public function getRepositoryList(): array
    {
        $response = $this->httpClient->get("$this->baseUrl/repository", $this->getRequestData());

        return $this->decodeList($response, Repository::class);
    }

	public function getRepositoryById(int $id): Repository
    {
        $response = $this->httpClient->get("$this->baseUrl/repository/$id", $this->getRequestData());

        return new Repository($this->decode($response));
    }

    public function getRepositoryByName(string $name): Repository
    {
        $response = $this->httpClient->get("$this->baseUrl/repository/$name", $this->getRequestData());

        return new Repository($this->decode($response));
    }

    public function updatePackageGroup(int $id, string $name): PackageGroup
    {
        $response = $this->httpClient->put("$this->baseUrl/package-group", $this->getRequestData([
            'id' => $id,
            'name' => $name,
        ]));
        return new PackageGroup($this->decode($response));
    }

    public function deletePackageGroup(int $id): array
    {
        $response = $this->httpClient->delete("$this->baseUrl/package-group/$id", $this->getRequestData());
        return $this->decode($response);
    }

    public function updateRepository(int $id, string $name): Repository
    {
        $response = $this->httpClient->put("$this->baseUrl/repository/$id", $this->getRequestData([
            'name' => $name,
        ]));
        return new Repository($this->decode($response));
    }

    public function deleteRepository(int $id): array
    {
        $response = $this->httpClient->delete("$this->baseUrl/repository/$id", $this->getRequestData());
        return $this->decode($response);
    }

    public function deletePackage(string $repositoryType, int $packageId): array
    {
        $response = $this->httpClient->delete("$this->baseUrl/package/$repositoryType/$packageId", $this->getRequestData());
        return $this->decode($response);
    }
}
