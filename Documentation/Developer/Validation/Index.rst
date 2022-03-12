.. include:: ../Includes.txt

.. _validation:

==========
Validation
==========

To set a field required you have to set the validation of that field:

.. code-block:: php

   $required = new FieldValidation();
   $required->setRequired(true);

   $title = new Field();
   $title->setName("title");
   $title->setLabel("Title");
   $title->setType(FieldType::TEXT);
   $title->setValidation($required);


