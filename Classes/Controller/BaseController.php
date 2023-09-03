<?php

declare(strict_types=1);

namespace Swisscode\Newt\Controller;

/***
 *
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2023 Jürgen Furrer <info@swisscode.sk>, SwissCode
 *
 ***/

/**
 * BaseController
 */
class BaseController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * Returns the value of a key from the array
     *
     * @param array $array
     * @param string $key
     * @return string|null
     */
    protected function getArrayKeyValue($array, $key): ?string
    {
        if (is_array($array) && array_key_exists($key, $array)) {
            return strval($array[$key]);
        }
        return null;
    }
}
