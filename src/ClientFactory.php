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

namespace wedocreatives\WrikePhpGuzzle;

use wedocreatives\WrikePhpGuzzle\Client\GuzzleClient;

/**
 * Client Factory.
 */
class ClientFactory
{
    /**
     * @throws \InvalidArgumentException
     *
     * @return GuzzleClient
     */
    public static function create(): GuzzleClient
    {
        return new GuzzleClient();
    }
}
