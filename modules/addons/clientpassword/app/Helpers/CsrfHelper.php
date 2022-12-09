<?php

namespace LMTech\ClientPassword\Helpers;

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

class CsrfHelper {

    public static function generate($key) {

        unset($_SESSION['csrf_' . $key]);
        $token = sha1(time() . self::randomKey());
        $_SESSION['csrf_' . $key] = $token;
        return $token;

    }

    public static function verify($key, $token) {

        $hash = $_SESSION[ 'csrf_' . $key ];
        if ( $token != $hash ) return false;
        return true;

    }

    protected static function randomKey($length = 10) {
       
        $seed = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijqlmnopqrtsuvwxyz0123456789';
        $max = strlen($seed) - 1;

        $string = '';
        for ( $i = 0; $i < $length; ++$i )
            $string .= $seed[intval( mt_rand( 0.0, $max ) )];

        return $string;
    }

}