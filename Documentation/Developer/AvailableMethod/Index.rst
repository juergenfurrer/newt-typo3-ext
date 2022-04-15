.. include:: ../Includes.txt

.. _fields:

======================
Available Method Types
======================

Here's an example of the method :code:`getAvailableMethodTypes`

.. code-block:: php

   public function getAvailableMethodTypes(): array
   {
      return [
         MethodType::CREATE,
         MethodType::READ,
         MethodType::UPDATE,
         MethodType::DELETE,
         MethodType::LIST,
      ];
   }

This method returns an array with all implemented methods
