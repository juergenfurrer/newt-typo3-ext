<?php

namespace Infonique\Newt\Domain\Model;

/***
 *
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2021 Jürgen Furrer <juergen@infonique.ch>, infonique, furrer
 *
 ***/
/**
 * FileReference
 */
class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference
{
    /**
     * We need this property so that the Extbase persistence can properly persist the object
     *
     * @var int
     */
    protected $uidLocal;

    /**
     * Returns the local UID
     *
     * @return void
     */
    public function getUidLocal()
    {
        return $this->uidLocal;
    }

    /**
     * @param \TYPO3\CMS\Core\Resource\File $falFile
     */
    public function setFile(\TYPO3\CMS\Core\Resource\File $falFile)
    {
        $this->uidLocal = (int)$falFile->getUid();
    }
}
