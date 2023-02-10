<?php

/*
 * This file is part of nimzero/stripe-bundle.
 * 
 * (c) NordLund Developpement <https://nordlund-dev.fr>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nordlund\CookieConsentBundle\DependencyInjection;

use Nordlund\CookieConsentBundle\Twig\NordlundCookieConsentTwigExtension;
use Nordlund\CookieConsentBundle\Twig\NordlundCookieConsentTwigRuntime;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * @author TESTA 'NimZero' Charly <contact@nimzero.fr>
 * 
 * @internal
 */
class NordlundCookieConsentExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
		$mergedConfig = $this->processConfiguration($configuration,$configs);

        $twigExtension = (new Definition(NordlundCookieConsentTwigExtension::class))
            ->setPublic(true)
            ->addTag('twig.extension')
        ;

        $twigRuntime = (new Definition(NordlundCookieConsentTwigRuntime::class))
            ->setPublic(true)
            ->setAutowired(true)
            ->setArgument('$configs', $mergedConfig)
            ->addTag('twig.runtime')
        ;

        $container->addDefinitions([
            'nordlund.cookie_consent.twig_extension' => $twigExtension,
            'nordlund.cookie_consent.twig_extension_runtime' => $twigRuntime,
        ]);
    }
}