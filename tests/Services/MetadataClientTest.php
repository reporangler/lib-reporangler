<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RepoRangler\Entity\PackageGroup;
use RepoRangler\Entity\Repository;
use RepoRangler\Services\MetadataClient;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class MetadataClientTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }

    private function createClient(Client $httpClient, string $token = 'test-token'): MetadataClient
    {
        return new MetadataClient('http://metadata.test', $httpClient, $token);
    }

    private function mockHttpResponse(string $body): Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], $body);
    }

    public function testGetPackageGroupListDecodesList()
    {
        $body = json_encode([
            'count' => 2,
            'data' => [
                ['id' => 1, 'name' => 'public'],
                ['id' => 2, 'name' => 'private'],
            ],
        ]);

        $httpClient = Mockery::mock(Client::class);
        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($this->mockHttpResponse($body));

        $client = $this->createClient($httpClient);
        $result = $client->getPackageGroupList();

        $this->assertArrayHasKey('data', $result);
        $this->assertCount(2, $result['data']);
        $this->assertInstanceOf(PackageGroup::class, $result['data']->first());
    }

    public function testGetPackageGroupListThrowsWhenNoDataKey()
    {
        $body = json_encode(['error' => 'bad']);

        $httpClient = Mockery::mock(Client::class);
        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($this->mockHttpResponse($body));

        $client = $this->createClient($httpClient);

        $this->expectException(UnprocessableEntityHttpException::class);
        $client->getPackageGroupList();
    }

    public function testGetRepositoryListUsesRepositoryClass()
    {
        $body = json_encode([
            'count' => 1,
            'data' => [
                ['id' => 1, 'name' => 'php'],
            ],
        ]);

        $httpClient = Mockery::mock(Client::class);
        $httpClient->shouldReceive('get')
            ->once()
            ->andReturn($this->mockHttpResponse($body));

        $client = $this->createClient($httpClient);
        $result = $client->getRepositoryList();

        $this->assertInstanceOf(Repository::class, $result['data']->first());
    }

    public function testGetHeadersIncludesToken()
    {
        $httpClient = Mockery::mock(Client::class);
        $client = $this->createClient($httpClient, 'test-token-123');

        $method = new \ReflectionMethod(MetadataClient::class, 'getHeaders');
        $method->setAccessible(true);
        $headers = $method->invoke($client);

        $this->assertSame('Bearer test-token-123', $headers['Authorization']);
        $this->assertSame('application/json', $headers['Accept']);
    }
}
