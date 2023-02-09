<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) NordLund Developpement <https://nordlund-dev.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nordlund\CookieConsentBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @author TESTA 'NimZero' Charly <contact@nimzero.fr>
 * 
 * @final
 */
class NordlundCookieConsentBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}