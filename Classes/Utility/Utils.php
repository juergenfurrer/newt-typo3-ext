<?php

namespace Infonique\Newt\Utility;

/***
 *
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2022 Jürgen Furrer <juergen@infonique.ch>, infonique, furrer
 *
 ***/
/**
 * Utils
 */
class Utils
{
    public static function getRequestHeader(string $name, \TYPO3\CMS\Extbase\Mvc\Request $request): ?string
    {
        if (method_exists($request, 'getHeader')) {
            return $request->getHeader($name)[0];
        }

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) == "HTTP_") {
                $key = str_replace(" ", "-", strtolower(str_replace("_", " ", substr($key, 5))));
                $headers[$key] = $value;
            } else {
                $headers[$key] = $value;
            }
        }

        $name = strtolower($name);
        if (isset($headers[$name])) {
            return $headers[$name];
        }

        return null;
    }
}
