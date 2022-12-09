<?php

use LMTech\ClientPassword\Admin\Admin;
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
 * @version    1.0.5
 * @link       https://leemahoney.dev
 */

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}

require_once __DIR__ . '/vendor/autoload.php';

function clientpassword_config() {
    return Config::populate();
}

function clientpassword_output($vars) {
    return Admin::output($vars);
}