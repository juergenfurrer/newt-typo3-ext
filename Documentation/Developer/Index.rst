.. include:: ../Includes.txt

.. _developer:

================
Developer Corner
================

To implement an endpoint into your own extension, you have to implement the EndpointInterface.

.. code-block:: php

   class MyEndpoint implements \Infonique\Newt\NewtApi\EndpointInterface {
      // implement the interface here
   }

As an example, please see EXT:newt4news/Classes/Newt/NewsEndpoint

For the Extension to know the available endpoint-implementations you have to add this Hook into your ext_localconf.php:

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['Newt']['Implementation'][] = \Infonique\Newt4News\Newt\NewsEndpoint::class;


.. _developer-api:

API
===

TBD
