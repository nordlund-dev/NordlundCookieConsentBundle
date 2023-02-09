<?php

/*
 * This file is part of nimzero/stripe-bundle.
 * 
 * (c) NordLund Developpement <https://nordlund-dev.fr>
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nordlund\CookieConsentBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * @author TESTA 'NimZero' Charly <contact@nimzero.fr>
 */
class NordlundCookieConsentTwigExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('cookie_consent', [NordlundCookieConsentTwigRuntime::class, 'cookieConsent'], ['is_safe' => ['html']]),
            new TwigFunction('is_consent_configured', [NordlundCookieConsentTwigRuntime::class, 'isConsentConfigured']),
            new TwigFunction('is_consent_granted', [NordlundCookieConsentTwigRuntime::class, 'isConsentGranted']),
        ];
    }
}