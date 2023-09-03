.. include:: ../Includes.txt

.. _developer:

================
Developer Corner
================

To implement an endpoint into your own extension, you have to implement the :code:`EndpointInterface`

.. code-block:: php

   class MyEndpoint implements \Swisscode\Newt\NewtApi\EndpointInterface
   {
      // implement the interface here
   }

As an example, please see :code:`EXT:newt4news/Classes/Newt/NewsEndpoint`


If your endpoint needs some additional options, you have to implement the :code:`EndpointOptionsInterface`

.. code-block:: php

   class MyEndpoint implements \Swisscode\Newt\NewtApi\EndpointInterface, \Swisscode\Newt\NewtApi\EndpointOptionsInterface
   {
      // implement the interface here
   }

As an example, please see :code:`EXT:newt4dce/Classes/Newt/DceEndpoint`


For the Extension to know the available endpoint-implementations you have to add this Hook into your ext_localconf.php:

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['Newt']['Implementation'][] = \Swisscode\Newt4News\Newt\NewsEndpoint::class;


.. toctree::
   :maxdepth: 5
   :titlesonly:

   AvailableMethod/Index
   EndpointOptions/Index
   Fields/Index
   Validation/Index
