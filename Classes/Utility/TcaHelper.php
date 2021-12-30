<?php

namespace Infonique\Newt\Utility;

/***
 *
 * This file is part of the "Billboard Manager" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2020 Jürgen Furrer <juergen@infonique.ch>, infonique, furrer
 *
 ***/
/**
 * TcaHelper
 */
class TcaHelper
{
    /**
     * Returns a list of Newt-Classes (called as itemsProcFunc).
     *
     * @param  array $configuration Current field configuration
     * @return void
     */
    public function getNewtClasses(array &$configuration)
    {
        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['Newt']['Implementation'] ?? [] as $className) {
            $configuration['items'][] = [$className, $className];
        }
    }
}
