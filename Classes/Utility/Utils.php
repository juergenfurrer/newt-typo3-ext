<?php

namespace Infonique\Newt\Utility;

use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

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

    /**
     * Returns the ApiUrl
     *
     * @return string
     */
    public static function getApiUrl(UriBuilder $uriBuilder): string
    {
        /** @var ConfigurationManager */
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);
        $conf = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $settings = $conf['plugin.']['tx_newt.']['settings.'] ?? [];

        $uri = $uriBuilder->reset()
                ->setTargetPageUid(intval($settings['apiPageId'] ?? 1))
                ->setTargetPageType(intval($settings['apiTypeNum']))
                ->setCreateAbsoluteUri(true)
                ->buildFrontendUri();
        $apiBaseUrl = trim($settings['apiBaseUrl'] ?? '');

        $base = '/';
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $sites = $siteFinder->getAllSites();

        // Get first site
        if ($site = reset($sites)) {
            $configuration = $site->getConfiguration();
            $base = $configuration['base'];
        }

        if ($apiBaseUrl != '') {
            if ($base == '/' && substr($apiBaseUrl, -1, 1) != '/') {
                // in case of base = "/" we have to add a slash to the url
                $apiBaseUrl .= '/';
            }
            return preg_replace('/^' . preg_quote($base, '/') . '/', $apiBaseUrl, $uri);;
        } else {
            return $uri;
        }
    }

    /**
     * Returns a new UUID
     *
     * @return string
     */
    public static function getUuid(): string
    {
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex(random_bytes(16)), 4));
    }
}
