<?php

declare(strict_types=1);

namespace Infonique\Newt\Domain\Model;


/**
 * This file is part of the "Newt" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * (c) 2021 Jürgen Furrer <juergen@infonique.ch>
 */

/**
 * EndpointOption
 */
class EndpointOption extends \TYPO3\CMS\Extbase\DomainObject\AbstractValueObject
{

    /**
     * optionName
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $optionName = '';

    /**
     * optionValue
     *
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $optionValue = '';


    /**
     * Get the value of optionName
     */
    public function getOptionName()
    {
        return $this->optionName;
    }

    /**
     * Set the value of optionName
     */
    public function setOptionName($optionName): self
    {
        $this->optionName = $optionName;
        return $this;
    }

    /**
     * Get the value of optionValue
     */
    public function getOptionValue()
    {
        return $this->optionValue;
    }

    /**
     * Set the value of optionValue
     */
    public function setOptionValue($optionValue): self
    {
        $this->optionValue = $optionValue;
        return $this;
    }
}
