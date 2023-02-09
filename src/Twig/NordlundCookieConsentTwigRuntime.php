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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @author TESTA 'NimZero' Charly <contact@nimzero.fr>
 */
class NordlundCookieConsentTwigRuntime implements RuntimeExtensionInterface
{
    private array $configs;
    private array $guiOptions;
    private Request $request;

    public function __construct(array $configs, array $guiOptions, RequestStack $requestStack)
    {
        $this->configs = $configs;
        $this->guiOptions = $guiOptions;
        $this->request = $requestStack->getCurrentRequest();
    }

    public function isConsentConfigured(string $configName = 'default')
    {
        $config = $this->configs[$configName];
        return $this->request->cookies->has($config['cookie_name']);
    }

    public function isConsentGranted(string $categorie, string $configName = 'default')
    {
        $config = $this->configs[$configName];

        $cookie = $this->request->cookies->get($config['cookie_name'], null);

        if (is_null($cookie)) {
            return false;
        }

        $cookie = json_decode($cookie, true);

        return in_array($categorie, $cookie['categories']);
    }

    public function cookieConsent(string $configName = 'default', string $guiOptionsName = 'default'): string
    {
        $config = $this->configs[$configName];
        $guiOption = $this->guiOptions[$guiOptionsName];

        $script = <<<EOD
        <script>
            window.addEventListener('load', function(){

                // obtain plugin
                var cc = initCookieConsent();
    
                // run plugin with your configuration
                cc.run({
                    autoclear_cookies: %s,
                    page_scripts: %s,
                    mode: '%s',
                    delay: %d,
                    auto_language: '%s',
                    autorun: %s,
                    force_consent: %s,
                    hide_from_bots: %s,
                    remove_cookie_tables: %s,
                    cookie_name: '%s',
                    cookie_expiration: %d,
                    cookie_necessary_only_expiration: %s,
                    cookie_domain: %s,
                    cookie_path: '%s',
                    cookie_same_site: '%s',
                    use_rfc_cookie: %s,
                    revision: %d,

                    gui_options: {
                        consent_modal: {
                            layout: '%s',
                            position: '%s',
                            transition: '%s',
                            swap_buttons: %s,
                        },
                        settings_modal: {
                            layout: '%s',
                            // position: '%s',
                            transition: '%s',
                        }
                    },

                    languages: {'en': {
                        consent_modal: {
                            title: 'We use cookies!',
                            description: 'Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it. The latter will be set only after consent. <button type="button" data-cc="c-settings" class="cc-link">Let me choose</button>',
                            primary_btn: {
                                text: 'Accept all',
                                role: 'accept_all'              // 'accept_selected' or 'accept_all'
                            },
                            secondary_btn: {
                                text: 'Reject all',
                                role: 'accept_necessary'        // 'settings' or 'accept_necessary'
                            }
                        },
                        settings_modal: {
                            title: 'Cookie preferences',
                            save_settings_btn: 'Save settings',
                            accept_all_btn: 'Accept all',
                            reject_all_btn: 'Reject all',
                            close_btn_label: 'Close',
                            cookie_table_headers: [
                                {col1: 'Name'},
                                {col2: 'Domain'},
                                {col3: 'Expiration'},
                                {col4: 'Description'}
                            ],
                            blocks: [
                                {
                                    title: 'Cookie usage 📢',
                                    description: 'I use cookies to ensure the basic functionalities of the website and to enhance your online experience. You can choose for each category to opt-in/out whenever you want. For more details relative to cookies and other sensitive data, please read the full <a href="#" class="cc-link">privacy policy</a>.'
                                }, {
                                    title: 'Strictly necessary cookies',
                                    description: 'These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly',
                                    toggle: {
                                        value: 'necessary',
                                        enabled: true,
                                        readonly: true          // cookie categories with readonly=true are all treated as "necessary cookies"
                                    }
                                }, {
                                    title: 'Performance and Analytics cookies',
                                    description: 'These cookies allow the website to remember the choices you have made in the past',
                                    toggle: {
                                        value: 'analytics',     // your cookie category
                                        enabled: false,
                                        readonly: false
                                    },
                                    cookie_table: [             // list of all expected cookies
                                        {
                                            col1: '^_ga',       // match all cookies starting with "_ga"
                                            col2: 'google.com',
                                            col3: '2 years',
                                            col4: 'description ...',
                                            is_regex: true
                                        },
                                        {
                                            col1: '_gid',
                                            col2: 'google.com',
                                            col3: '1 day',
                                            col4: 'description ...',
                                        }
                                    ]
                                }, {
                                    title: 'Advertisement and Targeting cookies',
                                    description: 'These cookies collect information about how you use the website, which pages you visited and which links you clicked on. All of the data is anonymized and cannot be used to identify you',
                                    toggle: {
                                        value: 'targeting',
                                        enabled: false,
                                        readonly: false
                                    }
                                }, {
                                    title: 'More information',
                                    description: 'For any queries in relation to our policy on cookies and your choices, please <a class="cc-link" href="#yourcontactpage">contact us</a>.',
                                }
                            ]
                        }
                    }}
                });
            });
        </script>
        EOD;

        $script = sprintf($script, 
            $config['autoclear_cookies'] ? 'true' : 'false',
            $config['page_scripts'] ? 'true' : 'false',
            $config['mode'],
            $config['delay'],
            $config['auto_language'] ?? 'null',
            $config['autorun'],
            $config['force_consent'] ? 'true' : 'false',
            $config['hide_from_bots'],
            $config['remove_cookie_tables'] ? 'true' : 'false',
            $config['cookie_name'],
            $config['cookie_expiration'],
            $config['cookie_necessary_only_expiration'] ?? 'null',
            $config['cookie_domain'],
            $config['cookie_path'],
            $config['cookie_same_site'],
            $config['use_rfc_cookie'] ? 'true' : 'false',
            $config['revision'],
            $guiOption['consent_modal']['layout'],
            $guiOption['consent_modal']['position'],
            $guiOption['consent_modal']['transition'],
            $guiOption['consent_modal']['swap_buttons'] ? 'true' : 'false',
            $guiOption['settings_modal']['layout'],
            $guiOption['settings_modal']['position'],
            $guiOption['settings_modal']['transition'],
            ''
        );

        return $script;
    }
}