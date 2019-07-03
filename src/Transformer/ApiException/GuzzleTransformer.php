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

namespace wedocreatives\WrikePhpGuzzle\Transformer\ApiException;

use GuzzleHttp\Exception\BadResponseException;
use wedocreatives\WrikePhpLibrary\Exception\Api\ApiException;
use wedocreatives\WrikePhpLibrary\Transformer\ApiException\AbstractApiExceptionTransformer;

/**
 * Guzzle Exception Transformer.
 */
class GuzzleTransformer extends AbstractApiExceptionTransformer
{
    /**
     * @param \Throwable $exception
     *
     * @return \Throwable|ApiException
     */
    public function transform(\Throwable $exception): \Throwable
    {
        if (false === $exception instanceof BadResponseException) {
            return new ApiException($exception);
        }

        try {
            /** @var BadResponseException $exception */
            $errorResponse = $exception->getResponse();
            if (null === $errorResponse) {
                return new ApiException($exception);
            }
            $errorStatusCode = $errorResponse->getStatusCode();
            $bodyString = (string) $errorResponse->getBody();
            $bodyArray = json_decode($bodyString, true);
            $errorStatusName = '';
            if (\is_array($bodyArray) && array_key_exists('error', $bodyArray)) {
                $errorStatusName = $bodyArray['error'];
            }
        } catch (\Exception $e) {
            return new ApiException($exception);
        }

        return $this->transformByStatusCodeAndName($exception, $errorStatusCode, $errorStatusName);
    }
}
