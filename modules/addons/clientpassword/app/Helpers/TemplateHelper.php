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

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}

class TemplateHelper {

    protected static $baseDir = __DIR__ . '/../../templates/';

    public static function checkExists($templateName) {

        $templateName = str_replace('.', '/', $templateName) . '.tpl';

        return (file_exists(self::$baseDir . $templateName)) ? true : false;

    }

    public static function getTemplate($templateName, $args = []) {

        $newTemplateName = str_replace('.', '/', $templateName);

        $smarty                 = new \Smarty;
        $smarty->caching        = false;
        $smarty->compile_dir    = $GLOBALS['templates_compiledir'];
        $smarty->template_dir   = self::$baseDir;
        $smarty->registerClass("PageHelper", "\LMTech\ClientPassword\Helpers\AdminPageHelper");

        if (self::checkExists($templateName)) {

            foreach ($args as $arg => $value) {
                $smarty->assign($arg, $value);
            }

            $smarty->display($newTemplateName . '.tpl');

        } else {

            $templateName = explode('.', $templateName)[0] . '/notfound.tpl';
            $smarty->display($templateName);

        }

    }

}