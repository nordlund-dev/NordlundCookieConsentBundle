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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author TESTA 'NimZero' Charly <contact@nimzero.fr>
 * 
 * @internal
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('nordlund_cookie_consent');

        $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('configurations')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                                ->defaultValue('default')
                            ->end() // name

                            ->booleanNode('autoclear_cookies')
                                ->defaultFalse()
                            ->end() // autoclear_cookies

                            ->booleanNode('page_scripts')
                                ->defaultFalse()
                            ->end() // page_scripts

                            ->enumNode('mode')
                                ->defaultValue('opt-in')
                                ->values(['opt-in', 'opt-out'])
                            ->end() // mode

                            ->integerNode('delay')
                                ->defaultValue(0)
                            ->end() // delay

                            ->enumNode('auto_language')
                                ->defaultNull()
                                ->values([null, 'browser', 'document'])
                            ->end() // auto_language

                            ->booleanNode('autorun')
                                ->defaultTrue()
                            ->end() // autorun

                            ->booleanNode('force_consent')
                                ->defaultFalse()
                            ->end() // force_consent

                            ->booleanNode('hide_from_bots')
                                ->defaultTrue()
                            ->end() // hide_from_bots

                            ->booleanNode('remove_cookie_tables')
                                ->defaultFalse()
                            ->end() // remove_cookie_tables
                            
                            ->scalarNode('cookie_name')
                                ->defaultValue('cc_cookie')
                            ->end() // cookie_name

                            ->integerNode('cookie_expiration')
                                ->defaultValue(182)
                            ->end() // cookie_expiration

                            ->scalarNode('cookie_necessary_only_expiration')
                                ->defaultNull()
                            ->end() // cookie_necessary_only_expiration

                            ->scalarNode('cookie_domain')
                                ->defaultValue('location.hostname')
                            ->end() // cookie_domain

                            ->scalarNode('cookie_path')
                                ->defaultValue('/')
                            ->end() // cookie_path

                            ->enumNode('cookie_same_site')
                                ->defaultValue('lax')
                                ->values(['lax', 'strict','none'])
                            ->end() // cookie_same_site

                            ->booleanNode('use_rfc_cookie')
                                ->defaultFalse()
                            ->end() // use_rfc_cookie

                            ->integerNode('revision')
                                ->defaultValue(0)
                            ->end() // revision
                        ->end()
                    ->end()
                    ->end()
                ->end() // configurations
            ->end();

        $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('gui_options')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                                ->defaultValue('default')
                            ->end() // name

                            ->arrayNode('consent_modal')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->enumNode('layout')
                                        ->defaultValue('box')
                                        ->values(['box', 'cloud', 'bar'])
                                    ->end() // layout

                                    ->scalarNode('position')
                                        ->defaultValue('bottom right')
                                    ->end() // position

                                    ->enumNode('transition')
                                        ->defaultValue('slide')
                                        ->values(['slide', 'zoom'])
                                    ->end() // transistion

                                    ->booleanNode('swap_buttons')
                                        ->defaultFalse()
                                    ->end() // swap_buttons
                                ->end()
                            ->end() // consent_modal

                            ->arrayNode('settings_modal')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->enumNode('layout')
                                        ->defaultValue('box')
                                        ->values(['box', 'bar'])
                                    ->end() // layout

                                    ->enumNode('position')
                                        ->defaultValue('left')
                                        ->values(['left', 'right'])
                                    ->end() // position

                                    ->enumNode('transition')
                                        ->defaultValue('slide')
                                        ->values(['slide', 'zoom'])
                                    ->end() // transition
                                ->end()
                            ->end() // settings_modal
                        ->end()
                    ->end()
                    ->end()
                ->end() // gui_options
            ->end();
        
            
        $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('languages')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')
                                ->defaultValue('en')
                            ->end() // name

                            ->arrayNode('consent_modal')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('title')
                                        ->defaultValue('We use cookies!')
                                    ->end() // title

                                    ->scalarNode('description')
                                        ->defaultValue('Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it. The latter will be set only after consent. <button type="button" data-cc="c-settings" class="cc-link">Let me choose</button>')
                                    ->end() // description

                                    ->arrayNode('primary_btn')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('text')
                                                ->defaultValue('Accept all')
                                            ->end() // text

                                            ->scalarNode('role')
                                                ->defaultValue('accept_all')
                                            ->end() // role
                                        ->end()
                                    ->end() // primary_btn

                                    ->arrayNode('secondary_btn')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('text')
                                                ->defaultValue('Reject all')
                                            ->end() // text

                                            ->scalarNode('role')
                                                ->defaultValue('accept_necessary')
                                            ->end() // role
                                        ->end()
                                    ->end() // secondary_btn
                                ->end()
                            ->end() // consent_modal

                            ->arrayNode('settings_modal')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('title')
                                        ->defaultValue('Cookie preferences')
                                    ->end() // title

                                    ->scalarNode('save_settings_btn')
                                        ->defaultValue('Save settings')
                                    ->end() // save_settings_btn

                                    ->scalarNode('accept_all_btn')
                                        ->defaultValue('Accept all')
                                    ->end() // accept_all_btn

                                    ->scalarNode('reject_all_btn')
                                        ->defaultValue('Reject all')
                                    ->end() // reject_all_btn

                                    ->scalarNode('close_btn_label')
                                        ->defaultValue('Close')
                                    ->end() // close_btn_label

                                    ->arrayNode('cookie_table_headers')
                                        ->scalarPrototype()
                                        ->end()
                                    ->end() // cookie_table_headers

                                    ->arrayNode('blocks')
                                        ->arrayPrototype()
                                        ->children()
                                            ->scalarNode('title')
                                            ->end() // title

                                            ->scalarNode('description')
                                            ->end() // description

                                            ->arrayNode('toggle')
                                                ->canBeUnset()
                                                ->children()
                                                    ->scalarNode('value')
                                                    ->end() // value

                                                    ->booleanNode('enabled')
                                                        ->defaultFalse()
                                                    ->end() // enabled

                                                    ->booleanNode('readonly')
                                                        ->defaultFalse()
                                                    ->end() // readonly
                                                ->end()
                                            ->end() // toggle

                                            ->arrayNode('cookie_table')
                                                ->canBeUnset()
                                                ->variablePrototype()
                                                ->end()
                                            ->end() // cookie_table
                                        ->end()
                                        ->end()
                                    ->end()

                                ->end()
                            ->end() // settings_modal
                        ->end()
                    ->end()
                    ->end()
                ->end() // gui_options
            ->end();

        return $treeBuilder;
	}
}