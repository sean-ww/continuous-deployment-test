<?php

namespace CD\services;

use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;
use \GuzzleHttp\Exception\ServerException;
use \GuzzleHttp\Exception\ConnectException;
use \GuzzleHttp\Exception\TransferException;

/**
 * A HTTP Client
 *
 * Setup Guzzle as the HTTP client with a method to handle request failures.
 *
 * @author Sean Wallis <sean.wallis2@networkrail.co.uk>
 */
class HttpClient
{
    /** @var Client $client Guzzle HTTP client. */
    protected $client;

    /**
     * HTTP Client constructor - Pull in dependencies.
     *
     * @param Client $client Guzzle HTTP client.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Try to make a get request whilst handling body errors
     *
     * If the body contains {"error":... then retry up to 5 times
     * before returning an empty result.
     *
     * @param string $url The url for the GET request.
     *
     * @return array An array of results.
     */
    public function tryGet($url)
    {
        try {
            $response = $this->client->get(
                $url,
                [
                    'headers' => [
                        'Accept' => 'application/json'
                    ]
                ]
            );

            return $this->handleResponseBodyErrors($response->getBody(), $url);
        } catch (ClientException $e) {
            // process 4xx...
        } catch (ServerException $e) {
            // process 5xx...
        } catch (ConnectException $e) {
            // process networking error...
        } catch (TransferException $e) {
            // process any other issue...
        }

        return [];
    }

    /**
     * Handle json Response Body Errors
     *
     * This is to handle errors with a 2xx status code.
     *
     * @param string $responseBody The json response.
     * @param string $url          The url for the GET request.
     * @param int    $retries      The current retry attempt number.
     * @param int    $maxRetries   The maximum number of retries allowed.
     *
     * @return array An array of results.
     */
    private function handleResponseBodyErrors($responseBody, $url, $retries = 0, $maxRetries = 5)
    {
        // Convert the json response body to an associative array
        $data = $this->jsonToAssoc($responseBody);

        // Check the response is not an error and return as an array
        if (!isset($data['error'])) {
            return $data;
        }

        // Limit the number of failures
        if ($retries > $maxRetries) {
            return [];
        }
        $retries++;

        // Retry
        return $this->tryGet($url, $retries, $maxRetries);
    }

    /**
     * Convert json to an associative array
     *
     * @param $json JSON to be converted.
     *
     * @return array Associative array of the json.
     */
    private function jsonToAssoc($json)
    {
        // Convert the json to an associative array
        $array = json_decode($json, true);

        // Verify an array gets returned
        if (is_array($array)) {
            return $array;
        }

        // Otherwise return an empty array
        return [];
    }
}
