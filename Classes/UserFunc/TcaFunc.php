<?php

class TcaFunc
{
    /**
     * Returns the data from the field and language submitted by $conf in JSON format
     *
     * @param string $content Empty string (no content to process)
     * @param array $conf TypoScript configuration
     * @return string JSON encoded data
     * @throws \InvalidArgumentException
     * @throws \TYPO3\CMS\Core\Exception\SiteNotFoundException
     */
    public function getFieldAsJson(string $content, array $conf): string
    {
        return '';
    }
}
