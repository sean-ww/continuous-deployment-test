<?php

namespace Unit\app\services;

use CD\services\HttpClient;

/**
 * HTTP Client Test
 *
 * @author Sean Wallis <sean.wallis2@networkrail.co.uk>
 */
class HttpClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $responseBody   The json response.
     * @param string $url            The url for the GET request.
     * @param int    $retries        The current retry attempt number.
     * @param int    $maxRetries     The maximum number of retries allowed.
     * @param int    $expectedResult What we expect our result to be.
     *
     * @dataProvider providerTestHandleResponseBodyErrorsSuccess
     */
    public function testHandleResponseBodyErrorsSuccess(
        $responseBody,
        $url,
        $retries,
        $maxRetries,
        $expectedResult
    ) {
        // Mock Guzzle
        $guzzleClientMock = $this
            ->getMockBuilder('\GuzzleHttp\Client')
            ->disableOriginalConstructor()
            ->getMock();

        // Instantiate the HttpClient
        $client = new HttpClient($guzzleClientMock);

        // use reflection and invokeMethod to test the private method
        $reflection = new \ReflectionClass(get_class($client));
        $handleResponseBodyErrors = $reflection->getMethod(
            'handleResponseBodyErrors'
        );
        $handleResponseBodyErrors->setAccessible(true);

        $actualResult = $handleResponseBodyErrors->invokeArgs(
            $client,
            [
                $responseBody,
                $url,
                $retries,
                $maxRetries
            ]
        );

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function providerTestHandleResponseBodyErrorsSuccess()
    {
        return [
            'product list' => [
                '{"login": "sean-ww", "type": "User"}',
                'https://my.url/api',
                0,
                5,
                [
                    'login' => 'sean-ww',
                    'type' => 'User'
                ]
            ]
        ];
    }
}
