<?php

namespace SparkPost\Test;

use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SparkPost\SparkPostResponse;

class SparkPostResponseTest extends TestCase
{
    /** @var Mockery\MockInterface|ResponseInterface */
    private $responseMock;

    public function setUp(): void
    {
        $this->responseMock = Mockery::mock(ResponseInterface::class);
    }

    public function tearDown(): void
    {
        Mockery::close();
    }

    public function testGetProtocolVersion()
    {
        $this->responseMock->shouldReceive('getProtocolVersion')->andReturn('1.1');
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame('1.1', $sparkpostResponse->getProtocolVersion());
    }

    public function testWithProtocolVersion()
    {
        $this->responseMock->shouldReceive('withProtocolVersion')->with('2')->andReturnSelf();
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame($this->responseMock, $sparkpostResponse->withProtocolVersion('2'));
    }

    public function testGetHeaders()
    {
        $headers = ['Content-Type' => ['application/json']];
        $this->responseMock->shouldReceive('getHeaders')->andReturn($headers);
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame($headers, $sparkpostResponse->getHeaders());
    }

    public function testHasHeader()
    {
        $this->responseMock->shouldReceive('hasHeader')->with('Content-Type')->andReturn(true);
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertTrue($sparkpostResponse->hasHeader('Content-Type'));
    }

    public function testGetHeader()
    {
        $header = ['application/json'];
        $this->responseMock->shouldReceive('getHeader')->with('Content-Type')->andReturn($header);
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame($header, $sparkpostResponse->getHeader('Content-Type'));
    }

    public function testGetHeaderLine()
    {
        $this->responseMock->shouldReceive('getHeaderLine')->with('Content-Type')->andReturn('application/json');
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame('application/json', $sparkpostResponse->getHeaderLine('Content-Type'));
    }

    public function testWithHeader()
    {
        $this->responseMock->shouldReceive('withHeader')->with('Content-Type', 'application/json')->andReturnSelf();
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame($this->responseMock, $sparkpostResponse->withHeader('Content-Type', 'application/json'));
    }

    public function testWithAddedHeader()
    {
        $this->responseMock->shouldReceive('withAddedHeader')->with('X-Test', 'value')->andReturnSelf();
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame($this->responseMock, $sparkpostResponse->withAddedHeader('X-Test', 'value'));
    }

    public function testWithoutHeader()
    {
        $this->responseMock->shouldReceive('withoutHeader')->with('X-Test')->andReturnSelf();
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame($this->responseMock, $sparkpostResponse->withoutHeader('X-Test'));
    }

    public function testGetRequest()
    {
        $request = ['some' => 'request'];
        $sparkpostResponse = new SparkPostResponse($this->responseMock, $request);

        $this->assertSame($request, $sparkpostResponse->getRequest());
    }

    public function testGetBody()
    {
        $body = Mockery::mock(StreamInterface::class);
        $this->responseMock->shouldReceive('getBody')->andReturn($body);
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame($body, $sparkpostResponse->getBody());
    }

    public function testGetDecodedBody()
    {
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn('{"results":{"id":"example"}}');
        $this->responseMock->shouldReceive('getBody')->andReturn($body);
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame(['results' => ['id' => 'example']], $sparkpostResponse->getDecodedBody());
    }

    /**
     * @dataProvider undecodableBodyProvider
     */
    public function testGetDecodedBodyReturnsNullForUndecodableBody($bodyContents)
    {
        $body = Mockery::mock(StreamInterface::class);
        $body->shouldReceive('__toString')->andReturn($bodyContents);
        $this->responseMock->shouldReceive('getBody')->andReturn($body);
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertNull($sparkpostResponse->getDecodedBody());
    }

    public function undecodableBodyProvider()
    {
        return [
            'empty body' => [''],
            'invalid JSON' => ['not-json'],
        ];
    }

    public function testWithBody()
    {
        $body = Mockery::mock(StreamInterface::class);
        $this->responseMock->shouldReceive('withBody')->with($body)->andReturnSelf();
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame($this->responseMock, $sparkpostResponse->withBody($body));
    }

    public function testGetStatusCode()
    {
        $this->responseMock->shouldReceive('getStatusCode')->andReturn(200);
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame(200, $sparkpostResponse->getStatusCode());
    }

    public function testWithStatus()
    {
        $this->responseMock->shouldReceive('withStatus')->with(202, 'Accepted')->andReturnSelf();
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame($this->responseMock, $sparkpostResponse->withStatus(202, 'Accepted'));
    }

    public function testGetReasonPhrase()
    {
        $this->responseMock->shouldReceive('getReasonPhrase')->andReturn('OK');
        $sparkpostResponse = new SparkPostResponse($this->responseMock);

        $this->assertSame('OK', $sparkpostResponse->getReasonPhrase());
    }
}
