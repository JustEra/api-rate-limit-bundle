<?php

/*
 * This file is part of the ApiRateLimitBundle
 *
 * (c) Indra Gunawan <hello@indra.my.id>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indragunawan\ApiRateLimitBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * The configuration of the bundle.
 *
 * @author Indra Gunawan <hello@indra.my.id>
 */
final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('indragunawan_api_rate_limit');

        $rootNode
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->scalarNode('cache')->defaultNull()->cannotBeEmpty()->end()
                ->arrayNode('header')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('display')->defaultTrue()->end()
                        ->arrayNode('names')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('limit')->cannotBeEmpty()->defaultValue('X-RateLimit-Limit')->end()
                                ->scalarNode('remaining')->cannotBeEmpty()->defaultValue('X-RateLimit-Remaining')->end()
                                ->scalarNode('reset')->cannotBeEmpty()->defaultValue('X-RateLimit-Reset')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('throttle')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('limit')->defaultValue(60)->end()
                        ->integerNode('period')->min(1)->defaultValue(60)->end()
                    ->end()
                ->end()
                ->arrayNode('exception')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('status_code')
                            ->defaultValue(Response::HTTP_TOO_MANY_REQUESTS)
                            ->validate()
                            ->ifNotInArray(array_keys(Response::$statusTexts))
                                ->thenInvalid('Invalid status code "%s"')
                            ->end()
                        ->end()
                        ->scalarNode('message')->cannotBeEmpty()->defaultValue('API rate limit exceeded for %s.')->end()
                        ->scalarNode('custom_exception')->cannotBeEmpty()->defaultNull()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}