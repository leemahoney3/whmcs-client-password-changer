<?php

namespace LMTech\ClientPassword\Helpers;

use LMTech\ClientPassword\Config\Config;

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
 * @version    1.0.6
 * @link       https://leemahoney.dev
 */

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}

class RedirectHelper {

    public static function to($url) {
        header("Location: {$url}");
        exit;
    }

    public static function page($page, $args = []) {

        $url = Config::getMaster('SystemURL') . $GLOBALS['customadminpath'] . "/addonmodules.php?module=clientpassword&page={$page}";

        if (!empty($args)) {
            foreach ($args as $arg => $value) {
                $url .= "&{$arg}={$value}";
            }
        }

        self::to($url);

    }

}