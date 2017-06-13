<?php

/*
 * This file is part of the ApiRateLimitBundle
 *
 * (c) Indra Gunawan <hello@indra.my.id>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indragunawan\ApiRateLimitBundle\Tests\DependencyInjection;

use Indragunawan\ApiRateLimitBundle\DependencyInjection\IndragunawanApiRateLimitExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class IndragunawanApiRateLimitExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @var IndragunawanApiRateLimitExtension
     */
    private $extension;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.cache_dir', '../../var/cache');
        // $this->container->setDefinition('custom_cache', new Definition(\Doctrine\Common\Cache\RedisCache::class));

        $this->extension = new IndragunawanApiRateLimitExtension();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        unset($this->container, $this->extension);
    }

    public function testEventListenerConfig()
    {
        $config = [
            [
                'enabled' => false,
            ],
        ];

        $this->extension->load($config, $this->container);

        $this->assertTrue($this->container->hasDefinition('indragunawan_api_rate_limit.event_listener.rate_limit'));
        $this->assertTrue($this->container->hasDefinition('indragunawan_api_rate_limit.event_listener.header_modification'));

        $rateLimitDefinition = $this->container->getDefinition('indragunawan_api_rate_limit.event_listener.rate_limit');
        $this->assertFalse($rateLimitDefinition->getArgument(0));
    }

    public function testServiceConfig()
    {
        $config = [
            [
                'cache' => 'custom_cache',
            ],
        ];

        $this->extension->load($config, $this->container);

        $this->assertTrue($this->container->hasDefinition('indragunawan_api_rate_limit.service.rate_limit_handler'));

        $cacheDefinition = $this->container->getDefinition('indragunawan_api_rate_limit.service.rate_limit_handler');
        $this->assertSame('custom_cache', (string) $cacheDefinition->getArgument(0));
    }
}