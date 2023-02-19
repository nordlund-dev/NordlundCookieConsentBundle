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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @author TESTA 'NimZero' Charly <contact@nimzero.fr>
 */
class NordlundCookieConsentTwigRuntime implements RuntimeExtensionInterface
{
    private array $configs;
    private array $guiOptions;
    private array $languages;
    private Request $request;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(array $configs, RequestStack $requestStack, UrlGeneratorInterface $urlGenerator)
    {
        $this->configs = $configs['configurations'];
        $this->guiOptions = $configs['gui_options'];
        $this->languages = $configs['languages'];
        $this->request = $requestStack->getCurrentRequest();
        $this->urlGenerator = $urlGenerator;
    }

    private function makeCookieTableHeaders(array $conf): string
    {
        $cookieTableHeaders = '';

        foreach ($conf as $key => $value) {
            $cookieTableHeaders .= sprintf("{%s: '%s'},", $key, $value);
        }

        return $cookieTableHeaders;
    }

    private function makeBlocks(array $blocks): string
    {
        $tmp = '';

        foreach ($blocks as $block) {
            $str = "{";

            foreach ($block as $key => $value) {
                if (is_string($value)) {
                    $str .= "$key: '$value',";
                }
                elseif (is_array($value) && count($value) > 0) {

                    if ($key === 'cookie_table') {
                        $str .= "$key: [";

                        foreach ($value as $subKey => $subValue) {
                            $str .= '{';
                            foreach ($subValue as $k => $v) {
                                $str .= "$k: '$v',";
                            }
                            $str.='},';
                        }

                        $str .= "],";
                    }
                    else {
                        $str .= "$key: {";

                        foreach ($value as $subKey => $subValue) {
                            $str .= "$subKey: '$subValue',";
                        }

                        $str .= "},";
                    }
                }
            }

            $tmp .= $str.'},';
        }

        return $tmp;
    }

    private function buildLanguages(): string
    {
        $languages = '';

        foreach ($this->languages as $lang => $conf) {
            $language = "'$lang': {consent_modal: {%s}, settings_modal: {%s}},";

            $consent = "title: '%s', description: '%s', primary_btn: {text: '%s', role: '%s'}, secondary_btn: {text: '%s', role: '%s'},";
            $consent = sprintf($consent,
                $conf['consent_modal']['title'],
                $conf['consent_modal']['description'],
                $conf['consent_modal']['primary_btn']['text'],
                $conf['consent_modal']['primary_btn']['role'],
                $conf['consent_modal']['secondary_btn']['text'],
                $conf['consent_modal']['secondary_btn']['role']
            );

            $settings = "title: '%s', save_settings_btn: '%s', accept_all_btn: '%s', reject_all_btn: '%s', close_btn_label: '%s', cookie_table_headers: [%s], blocks: [%s]";
            $settings = sprintf($settings,
                $conf['settings_modal']['title'],
                $conf['settings_modal']['save_settings_btn'],
                $conf['settings_modal']['accept_all_btn'],
                $conf['settings_modal']['reject_all_btn'],
                $conf['settings_modal']['close_btn_label'],
                $this->makeCookieTableHeaders($conf['settings_modal']['cookie_table_headers']),
                $this->makeBlocks($conf['settings_modal']['blocks'])
            );

            $language = sprintf($language, $consent, $settings);

            preg_match_all("/!!route:([a-z0-9_-]+)!!/i", $language, $hashtweet);
            foreach ($hashtweet[0] as $index => $ht){
                $url = $this->urlGenerator->generate($hashtweet[1][$index]);
                $language = str_replace($ht, $url, $language);
            }    

            $languages .= $language;
        }

        return $languages;
    }

    public function isConsentConfigured(string $configName = 'default'): bool
    {
        $config = $this->configs[$configName];
        return $this->request->cookies->has($config['cookie_name']);
    }

    public function isConsentGranted(string $categorie, string $configName = 'default'): bool
    {
        $config = $this->configs[$configName];

        $cookie = $this->request->cookies->get($config['cookie_name'], null);

        if (is_null($cookie)) {
            return false;
        }

        $cookie = json_decode($cookie, true);

        return in_array($categorie, $cookie['categories']);
    }

    public function scriptConsent(string $categorie):string
    {
        return sprintf('type="text/plain" data-cookiecategory="%s"', $categorie);
    }

    public function cookieConsent(string $configName = 'default', string $guiOptionsName = 'default'): string
    {
        $config = $this->configs[$configName];
        $guiOption = $this->guiOptions[$guiOptionsName];

        $script = <<<EOD
        <script>
            window.addEventListener('load', function(){

                window.nordlundCookieConsent = initCookieConsent();
    
                nordlundCookieConsent.run({
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
                    onFirstAction: nordlundCookieOnFirstAction,
                    onAccept: nordlundCookieOnAccept,
                    onChange: nordlundCookieOnChange,
                    gui_options: {consent_modal: {layout: '%s',position: '%s',transition: '%s',swap_buttons: %s,},settings_modal: {layout: '%s',position: '%s',transition: '%s',}},
                    languages: {%s}
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
            $this->buildLanguages()
        );

        return $script;
    }
}