<?php

use LMTech\ClientPassword\Config\Config;
use LMTech\ClientPassword\Helpers\CsrfHelper;

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


function add_clientpassword_buttons($vars) {


    if(Config::get('showButtons') && (preg_match("/\/client\/([1-9])\/users\b/", $_SERVER['REQUEST_URI']) || preg_match("/\/user\/list\b/", $_SERVER['REQUEST_URI']))) {
        
        $currentUser = new \WHMCS\Authentication\CurrentUser;

        if ($currentUser->admin()) {
            if (!in_array($currentUser->admin()->roleid, explode(',', Config::get('access')))) {
                return '';
            }
        }

        $systemURL = Config::getMaster('SystemURL');
        $csrfToken = CsrfHelper::generate(Config::get('csrfKey'));

        if (Config::get('showModal')) {
            return '
                <div class="modal whmcs-modal fade in" id="modalChangePw" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content panel panel-primary">
                            <div class="modal-header panel-heading" id="modalChangePwHeader">
                                <button id="modalChangePwCloseSmall" type="button" class="close" data-dismiss="modal">
                                    <span aria-hidden="true">&times;</span>
                                    <span class="sr-only">Close</span>
                                </button>
                                <h4 class="modal-title" id="modalChangePwTitle">Change Password for: <span id="usrName">N/A</span> (<span id="usrEmail">N/A</span>)</h4>
                            </div>
                            <div class="modal-body panel-body" id="modalChangePwBody">
                                <div class="alert alert-danger admin-modal-error modalChangePwAlert" style="display: none"></div>
                                <div class="admin-tabs-v2">
                                    <div class="tab-content">
                                        <div class="tab-pane active">
                                            <div class="form-group">
                                                <label for="newPasswordInput" class="col-md-2 col-sm-4 control-label">New Password</label>
                                                <div class="col-md-10 col-sm-8">
                                                    <div class="input-group">
                                                        <input type="text" id="newPasswordInput" class="form-control" value="">
                                                        <span class="input-group-addon"><a href="#" id="genPW"><i class="glyphicon glyphicon-repeat"></i></a></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" id="usrID" value="">
                            </div>
                            <div class="modal-footer panel-footer" id="modalChangePwFooter">
                                <div id="modalFooterLeft"></div>
                                <div class="pull-left loader" id="modalChangePwLoader" style="display: none;">
                                    <i class="fas fa-circle-notch fa-spin"></i>
                                    Loading...
                                </div>
                                <button id="modalChangePwClose" type="button" class="btn btn-default" data-dismiss="modal">
                                    Close
                                </button>
                                <button type="button" class="btn btn-primary modal-submit" id="btnSave">Change</button>
                            </div>
                        </div>
                    </div>
                </div>
                <script>
                    $(".btn-permissions").each(function (i, obj) {
                        $(this).after(\'<a href="#" data-user-id="\' + $(this).attr("data-user-id") + \'" class="btn btn-default changePwBtn">Change Password</a>\');
                    });

                    $(".open-modal.manage-user").each(function (i, obj) {

                        let id = $(this).attr("href").replace( /^\D+/g, "");

                        $(this).after(\'<a href="#" data-user-id="\' + id + \'" class="btn btn-default btn-sm changePwBtn">Change Pass</a>\');
                    });

                    $(document).ready(function () {

                        $(".changePwBtn").on("click", function (e) {
                            
                            e.preventDefault();

                            let userID = $(this).attr("data-user-id");

                            $.ajax({
                                type: "POST",
                                url: "' . $systemURL . $GLOBALS['customadminpath'] . '/addonmodules.php?module=clientpassword&page=data",
                                data: "action=grab&user=" + userID + "&token=' . $csrfToken . '",
                                beforeSend: function () {
                                    $("#modalChangePwLoader").fadeIn();
                                    $(".modalChangePwAlert").removeClass("alert-success").addClass("alert-danger").html("").hide();
                                    $("#newPasswordInput").val("");
                                },
                                success: function (res) {
                                    res = JSON.parse(res);
                                    $("#usrID").val(userID);
                                    $("#usrName").html(res.data.fullName);
                                    $("#usrEmail").html(res.data.email);
                                },
                                complete: function () {
                                    $("#modalChangePwLoader").fadeOut();
                                    $("#modalChangePw").modal("show");
                                }
                            });

                        });

                        $("#btnSave").on("click", function (e) {
                            e.preventDefault();
                            
                            let userID = $("#usrID").val();
                            let password = encodeURIComponent($("#newPasswordInput").val());

                            $.ajax({
                                type: "POST",
                                url: "' . $systemURL . $GLOBALS['customadminpath'] . '/addonmodules.php?module=clientpassword&page=data",
                                data: "action=change&user=" + userID + "&pw=" + password + "&token=' . $csrfToken . '",
                                success: function (res) {
                                    res = JSON.parse(res);

                                    if (res.status == "success") {
                                        $(".modalChangePwAlert").removeClass("alert-danger").addClass("alert-success");
                                    } else {
                                        $(".modalChangePwAlert").removeClass("alert-success").addClass("alert-danger");
                                    }

                                    $(".modalChangePwAlert").html(res.data).fadeIn();

                                }
                            });

                        });

                        $("#genPW").on("click", function (e) {

                            e.preventDefault();

                            var length  = ' . Config::get('passwordLength') . ',
                            charset = "abcdefghijklmnopqrstuvwxyz",
                            retVal  = "";
            
                            ' . (Config::get('capitalLetters') ? 'charset += "ABCDEFGHIJKLMNOPQRSTUVWXYZ";' : '') . '
                            ' . (Config::get('numbers') ? 'charset += "0123456789";' : '') . '
                            ' . (Config::get('specialChars') ? 'charset += "+-*/$£%!&";' : '') . '

                            for (var i = 0, n = charset.length; i < length; ++i) {
                                retVal += charset.charAt(Math.floor(Math.random() * n));
                            }
                
                            $("#newPasswordInput").val(retVal);

                        });

                        $("#newPasswordInput").keyup(function (e) {

                            if (e.which === 13) {
                                $("#btnSave").click();
                            }
            
                        });

                    });

                </script>
            ';
        } else {
            return '
                <script>
                    $(".btn-permissions").each(function (i, obj) {
                        $(this).after(\'<a href="' . $systemURL . $GLOBALS['customadminpath'] . '/addonmodules.php?module=clientpassword&page=change&id=\' + $(this).attr("data-user-id") + \'" class="btn btn-default changePwBtn">Change Password</a>\');
                    });

                    $(".open-modal.manage-user").each(function (i, obj) {

                        let id = $(this).attr("href").replace( /^\D+/g, "");

                        $(this).after(\'<a href="' . $systemURL . $GLOBALS['customadminpath'] . '/addonmodules.php?module=clientpassword&page=change&id=\' + id + \'" class="btn btn-default btn-sm changePwBtn">Change Pass</a>\');
                    });
                </script>
            ';
        }

    }

}

add_hook('AdminAreaFooterOutput', 1, 'add_clientpassword_buttons');