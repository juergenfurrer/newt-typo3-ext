<?php

namespace Swisscode\Newt\Utility;

use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
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
 *  (c) 2022 Jürgen Furrer <info@swisscode.sk>, SwissCode
 *
 ***/
/**
 * Utils
 */
class Utils
{
    public static function isTrue($val, $return_null=false)
    {
        $boolval = is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool)$val;
        return $boolval === null && !$return_null ? false : $boolval;
    }

    public static function getRequestHeader(string $name, \TYPO3\CMS\Extbase\Mvc\Request $request): ?string
    {
        if (method_exists($request, 'getHeader')) {
            if (isset($request->getHeader($name)[0])) {
                return $request->getHeader($name)[0];
            }
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
     * REturns the maximum upload size in Bytes
     *
     * @return integer
     */
    public static function getFileUploadMaxSize(): int
    {
        static $max_size = -1;

        if ($max_size < 0) {
            // Start with post_max_size.
            $post_max_size = Utils::parseIniSize(ini_get('post_max_size'));
            if ($post_max_size > 0) {
                $max_size = $post_max_size;
            }

            // If upload_max_size is less, then reduce. Except if upload_max_size is
            // zero, which indicates no limit.
            $upload_max = Utils::parseIniSize(ini_get('upload_max_filesize'));
            if ($upload_max > 0 && $upload_max < $max_size) {
                $max_size = $upload_max;
            }
        }

        return $max_size;
    }

    /**
     * Parse the ini-setting to bytes
     *
     * @param string $size
     * @return integer
     */
    public static function parseIniSize($size): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
        $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
        if ($unit && isset($unit[0])) {
            // Find the position of the unit in the ordered string which is the power of magnitude to multiply a kilobyte by.
            return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
        } else {
            return round($size);
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


    /**
     * Check for TYPO3 Version 10
     *
     * @return bool True if TYPO3 is version 10
     */
    public static function isVersion10(): bool
    {
        $typo3versionAsInt = VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getCurrentTypo3Version());
        return $typo3versionAsInt > 10000000 && $typo3versionAsInt < 11000000;
    }

    /**
     * Check for TYPO3 Version 11
     *
     * @return bool True if TYPO3 is version 11
     */
    public static function isVersion11(): bool
    {
        $typo3versionAsInt = VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getCurrentTypo3Version());
        return $typo3versionAsInt > 11000000 && $typo3versionAsInt < 12000000;
    }

    /**
     * Check for TYPO3 Version 12
     *
     * @return bool True if TYPO3 is version 12
     */
    public static function isVersion12(): bool
    {
        $typo3versionAsInt = VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getCurrentTypo3Version());
        return $typo3versionAsInt > 12000000 && $typo3versionAsInt < 13000000;
    }

    /**
     * Check for TYPO3 Version 12+
     *
     * @return bool True if TYPO3 is version 12+
     */
    public static function isVersion12Plus(): bool
    {
        $typo3versionAsInt = VersionNumberUtility::convertVersionNumberToInteger(VersionNumberUtility::getCurrentTypo3Version());
        return $typo3versionAsInt >= 12000000;
    }
}
