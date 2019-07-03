<?php

declare(strict_types=1);

/*
 * This file is part of the wedocreatives/wrike-php-guzzle package.
 *
 * (c) Zbigniew Ślązak
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace wedocreatives\WrikePhpGuzzle\Client;

use GuzzleHttp\Client as BaseClient;
use Psr\Http\Message\ResponseInterface;
use wedocreatives\WrikePhpLibrary\Api;
use wedocreatives\WrikePhpLibrary\Client\ClientInterface;
use wedocreatives\WrikePhpLibrary\Enum\Api\RequestMethodEnum;
use wedocreatives\WrikePhpLibrary\Exception\Api\ApiException;
use wedocreatives\WrikePhpLibrary\Validator\AccessTokenValidator;

/**
 * Guzzle Client for Wrike library.
 */
class GuzzleClient extends BaseClient implements ClientInterface
{
    /**
     * Request method.
     *
     * Generic format for HTTP client request method.
     *
     * @param string $requestMethod GET/POST/PUT/DELETE
     * @param string $path          full path to REST resource without domain, ex. 'contacts'
     * @param array  $params        optional params for GET/POST request
     * @param string $accessToken   Access Token for Wrike access
     *
     * @see \wedocreatives\WrikePhpLibrary\Enum\Api\RequestMethodEnum
     * @see \wedocreatives\WrikePhpLibrary\Enum\Api\RequestPathFormatEnum
     *
     * @throws \Throwable
     * @throws ApiException
     *
     * @return ResponseInterface
     */
    public function executeRequestForParams(
        string $requestMethod,
        string $path,
        array $params,
        string $accessToken
    ): ResponseInterface {
        RequestMethodEnum::assertIsValidValue($requestMethod);
        $options = $this->calculateOptionsForParams($requestMethod, $params, $accessToken);
        if (RequestMethodEnum::UPLOAD === $requestMethod) {
            $requestMethod = RequestMethodEnum::POST;
        }

        return $this->request($requestMethod, $path, $options);
    }

    /**
     * Main method for calculating request params.
     *
     * @param string $requestMethod
     * @param array  $params
     * @param string $accessToken
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function calculateOptionsForParams(string $requestMethod, array $params, string $accessToken): array
    {
        $options = $this->prepareBaseOptions($accessToken);
        if (0 === \count($params)) {
            return $options;
        }

        switch ($requestMethod) {
            case RequestMethodEnum::GET:
            case RequestMethodEnum::DELETE:
                $options['query'] = $params;
                break;
            case RequestMethodEnum::PUT:
            case RequestMethodEnum::POST:
                $options['form_params'] = $params;
                break;
            case RequestMethodEnum::UPLOAD:
                if (array_key_exists('resource', $params) && array_key_exists('name', $params)) {
                    $options['headers']['X-File-Name'] = $params['name'];
                    $options['multipart'] = [
                        [
                            'contents' => $params['resource'],
                            'name' => $params['name'],
                        ],
                    ];
                }
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Request method "%s" not allowed!', $requestMethod));
        }

        return $options;
    }

    /**
     * @param string $accessToken
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function prepareBaseOptions(string $accessToken): array
    {
        AccessTokenValidator::assertIsValid($accessToken);
        $options = [];
        $options['headers']['Authorization'] = sprintf('Bearer %s', $accessToken);
        $options['base_uri'] = Api::BASE_URI;

        return $options;
    }
}
