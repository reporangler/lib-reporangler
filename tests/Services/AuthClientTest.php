<?php

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RepoRangler\Services\AuthClient;

class AuthClientTest extends TestCase
{
    protected function tearDown()
    {
        Mockery::close();
    }

    private function mockHttpResponse(string $body): Response
    {
        return new Response(200, ['Content-Type' => 'application/json'], $body);
    }

    public function testLoginSendsCorrectHeaders()
    {
        $httpClient = Mockery::mock(Client::class);
        $httpClient->shouldReceive('get')
            ->once()
            ->withArgs(function ($url, $options) {
                return $url === 'http://auth.test/login/api'
                    && $options['headers']['reporangler-login-type'] === 'database'
                    && $options['headers']['reporangler-login-username'] === 'admin'
                    && $options['headers']['reporangler-login-password'] === 'secret';
            })
            ->andReturn($this->mockHttpResponse(json_encode(['token' => 'abc'])));

        $client = new AuthClient('http://auth.test', $httpClient);
        $result = $client->login('database', 'admin', 'secret');

        $this->assertSame('abc', $result['token']);
    }

    public function testCheckStripsBearer()
    {
        $httpClient = Mockery::mock(Client::class);
        $httpClient->shouldReceive('get')
            ->once()
            ->withArgs(function ($url, $options) {
                return $url === 'http://auth.test/login/token'
                    && $options['headers']['Authorization'] === 'Bearer mytoken123';
            })
            ->andReturn($this->mockHttpResponse(json_encode(['valid' => true])));

        $client = new AuthClient('http://auth.test', $httpClient);
        $result = $client->check('Bearer mytoken123');

        $this->assertTrue($result['valid']);
    }

    public function testCheckThrowsForPublicToken()
    {
        $httpClient = Mockery::mock(Client::class);
        $client = new AuthClient('http://auth.test', $httpClient);

        $this->expectException(\InvalidArgumentException::class);
        $client->check('public');
    }
}
