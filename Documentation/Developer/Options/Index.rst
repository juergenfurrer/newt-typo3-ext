.. include:: ../Includes.txt

.. _fields:

================
Endpoint Options
================

Here's an example of how to implement the :code:`EndpointOptionsInterface`

.. code-block:: php

   /**
    * Pass one EndpointOption to the class
    *
    * @param string $optionName
    * @param string $optionValue
    * @return void
    */
   public function addEndpointOption(string $optionName, string $optionValue): void
   {
      switch ($optionName) {
         case "pluginName" :
            $this->pluginName = $optionValue;
            break;
         case "fieldTitle" :
            $this->fieldTitle = $optionValue;
            break;
         case "fieldDescription" :
            $this->fieldDescription = $optionValue;
            break;
      }
   }

   /**
    * Returns the array with needed options as an assoc-array ["key" => "label"]
    *
    * @return array
    */
   public function getNeededOptions(): array
   {
      $languageFile = 'LLL:EXT:newt4dce/Resources/Private/Language/locallang_db.xlf:';
      return [
         $GLOBALS['LANG']->sL($languageFile . 'tx_newt4dce.options.pluginName')       => "pluginName",
         $GLOBALS['LANG']->sL($languageFile . 'tx_newt4dce.options.fieldTitle')       => "fieldTitle",
         $GLOBALS['LANG']->sL($languageFile . 'tx_newt4dce.options.fieldDescription') => "fieldDescription",
      ];
   }

   /**
    * Returns the hint for EndpointOptions
    *
    * @return string|null
    */
   public function getEndpointOptionsHint(): ?string
   {
      $languageFile = 'LLL:EXT:newt4dce/Resources/Private/Language/locallang_db.xlf:';
      return $GLOBALS['LANG']->sL($languageFile . 'tx_newt4dce.options.hint');
   }


