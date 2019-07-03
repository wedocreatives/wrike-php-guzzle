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

namespace wedocreatives\WrikePhpGuzzle\Tests;

use GuzzleHttp\ClientInterface;
use wedocreatives\WrikePhpGuzzle\Client\GuzzleClient;
use wedocreatives\WrikePhpGuzzle\ClientFactory;

/**
 * Client Factory Test.
 */
class ClientFactoryTest extends TestCase
{
    public function test_create(): void
    {
        $client = ClientFactory::create();
        self::assertInstanceOf(ClientInterface::class, $client);
        self::assertInstanceOf(GuzzleClient::class, $client);
    }
}
