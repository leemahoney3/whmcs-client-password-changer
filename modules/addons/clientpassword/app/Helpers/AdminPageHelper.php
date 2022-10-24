<?php

namespace LMTech\ClientPassword\Helpers;

use WHMCS\Config\Setting;
use LMTech\ClientPassword\Helpers\RedirectHelper;
use LMTech\ClientPassword\Helpers\TemplateHelper;

/**
 * WHMCS Client Password Changer
 *
 * Allows admins to change a users password manually without the need to send an email
 * to the client and reset it that way.
 *
 * @package    WHMCS
 * @author     Lee Mahoney <lee@leemahoney.dev>
 * @copyright  Copyright (c) Lee Mahoney 2022
 * @license    MIT License
 * @version    1.0.4
 * @link       https://leemahoney.dev
 */

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}

class AdminPageHelper {

    protected static $pages = [
        [
            'name' => 'Dashboard',
            'slug' => 'dashboard',
        ],
        [
            'name' => 'Change Password',
            'slug' => 'change',
        ],
        [
            'name' => 'Data Output',
            'slug' => 'data',
        ],
    ];

    public static function pageExists($page) {
        return (self::getAttribute($page) && !empty(self::getAttribute($page)) && in_array(self::getCurrentPage(), array_column(self::getAllPages(), 'slug'))) ? true : false;
    }

    public static function getPage($page, $args) {
        TemplateHelper::getTemplate($page, $args);
    }

    public static function getAllPages() {
        return self::$pages;
    }

    public static function getCurrentPage() {
        return (!empty(self::getAttribute('page'))) ? self::getAttribute('page') : 'none';
    }

    public static function outputPage($args) {

        $args['systemURL'] = Setting::getValue('SystemURL');

        if (in_array(self::getCurrentPage(), array_column(self::getAllPages(), 'slug'))) {

            foreach (self::getAllPages() as $page) {

                if (self::getCurrentPage() != $page['slug']) {
                    continue;
                }

                self::getPage("admin.{$page['slug']}", $args);

            }

        } else {
            RedirectHelper::page('dashboard');
        }

    }

    public static function getAction() {
        return self::getAttribute('action');
    }

    public static function getAttribute($attr) {
        return $_REQUEST[$attr];
    }

}