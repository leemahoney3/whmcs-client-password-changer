<?php

namespace LMTech\ClientPassword\Admin;

use WHMCS\User\User;
use WHMCS\Database\Capsule;
use WHMCS\Authentication\CurrentUser;
use LMTech\ClientPassword\Config\Config;
use LMTech\ClientPassword\Helpers\CsrfHelper;
use LMTech\ClientPassword\Helpers\RedirectHelper;
use LMTech\ClientPassword\Helpers\AdminPageHelper;
use LMTech\ClientPassword\Helpers\PaginationHelper;

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

class Admin {

    public static function output($vars) {

        $passThru = [
            'moduleLink'        => $vars['modulelink'],
            'updateAvailable'   => Config::checkForUpdate(),
            'alert'             => [],
            'formData'          => [],
        ];

        if(AdminPageHelper::getCurrentPage() == 'dashboard') {

            $where = [];

            if (isset($_POST['search']) && !empty($_POST['search'])) {

                $query = stripslashes($_POST['search']);
                $where = [
                    [Capsule::raw('concat(`first_name`, " ", `last_name`)'), 'like', '%' . $query . '%'],
                    ['email', 'like', '%' . $query . '%'],
                ];

            }

            $pagination = new PaginationHelper('p', [], Config::get('paginationLimit'), \WHMCS\User\User::class, [], $where, [Config::get('paginationSortOrder'), Config::get('paginationSortField')]);

            $passThru['users']              = $pagination->data();
            $passThru['paginationLinks']    = $pagination->links();

        }

        if (AdminPageHelper::getCurrentPage() == 'change') {
            
            $id = (int) AdminPageHelper::getAttribute('id');

            $user = User::where('id', $id)->first();

            if (!$user) {
                RedirectHelper::page('dashboard', ['error' => 'nouser']);
            }

            $passThru['user'] = $user;
            
            if (!isset($_POST['newPw'])) {
                $passThru['csrfToken'] = CsrfHelper::generate(Config::get('csrfKey'));
            }

            if (isset($_POST['newPw']) && !empty($_POST['newPw'])) {

                if (!CsrfHelper::verify(Config::get('csrfKey'), htmlspecialchars($_POST['token']))) {
                    die(json_encode([
                        'status'    => 'error',
                        'message'   => 'CSRF Check Failed. Please try again.',
                    ]));
                }

                User::where('id', $user->id)->first()->updatePassword(trim($_POST['newPw']));

                if (Config::get('enableLogging')) {
                    foreach ($user->ownedClients() as $client) {
                        logActivity("User password manually changed by admin (User ID: {$user->id})", $client->id);
                    }
                }

                RedirectHelper::page('dashboard', ['success' => 'changed']);

            } else if(isset($_POST['newPw']) && empty($_POST['newPw'])) {

                $passThru['alert'] = [
                    'type'      => 'error',
                    'message'   => 'Please enter a password'
                ];

            }

            $passThru['generatorScript'] = '
                <script type="text/javascript">
                    function generatePassword() {
                        var length  = ' . Config::get('passwordLength') . ',
                            charset = "abcdefghijklmnopqrstuvwxyz",
                            retVal  = "";
            
                        ' . (Config::get('capitalLetters') ? 'charset += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";' : '') . '
                        ' . (Config::get('numbers') ? 'charset += "0123456789";' : '') . '
                        ' . (Config::get('specialChars') ? 'charset += "+-*/$£%!&";' : '') . '

                        for (var i = 0, n = charset.length; i < length; ++i) {
                            retVal += charset.charAt(Math.floor(Math.random() * n));
                        }
            
                        document.getElementById("newPw").setAttribute("value", retVal);
                
                    }
                </script>
            ';

        }

        if (AdminPageHelper::getCurrentPage() == 'data') {

            if (!Config::get('showModal')) {
                die(json_encode([
                    'status'    => 'error',
                    'data'      => 'This module function is not enabled.',
                ]));
            }

            if (!CsrfHelper::verify(Config::get('csrfKey'), htmlspecialchars($_POST['token']))) {
                die(json_encode([
                    'status'    => 'error',
                    'message'   => 'CSRF Check Failed. Please try again.',
                ]));
            }

            $currentUser = new CurrentUser;

            # Just incase...
            if ($currentUser->admin()) {
                if (!in_array($currentUser->admin()->roleid, explode(',', Config::get('access')))) {
                    die(json_encode([
                        'status' => 'error', 
                        'data' => 'Your operator role does not have access to this function.'
                    ]));
                }
            }

            if (!isset($_POST) || empty($_POST)) {
                exit('This page cannot be accessed directly.');
            }
            
            switch ($_POST['action']) {
                
                case 'grab':

                    $userID = (int) trim($_POST['user']);
                    $user   = User::select('first_name', 'last_name', 'email')->where('id', $userID)->first();

                    die(json_encode([
                        'status'        => 'success',
                        'data'          => [
                            'fullName'  => $user->first_name . ' ' . $user->last_name,
                            'email'     => $user->email,
                        ],
                    ]));

                    break;

                case 'change':

                    $userID     = (int) trim($_POST['user']);
                    $password   = trim(stripslashes(html_entity_decode($_POST['pw'])));
                    $user       = User::where('id', $userID)->first();

                    if (empty($password)) {
                        $data = [
                            'status'    => 'error',
                            'data'      => 'Please enter a password',
                        ];
                    } else if (empty($userID)) {
                        $data = [
                            'status'    => 'error',
                            'data'      => 'Invalid user ID provided',
                        ];
                    } else {

                        User::where('id', $user->id)->first()->updatePassword($password);

                        if (Config::get('enableLogging')) {
                            foreach ($user->ownedClients() as $client) {
                                logActivity("User password manually changed by admin (User ID: {$user->id})", $client->id);
                            }
                        }

                        $data = [
                            'status'    => 'success',
                            'data'      => "The password has been updated for user: <b>{$user->first_name} {$user->last_name} ({$user->email})</b>"
                        ];
                    }

                    die(json_encode($data));

                    break;
            
            }

        }

        AdminPageHelper::outputPage($passThru);

    }

}