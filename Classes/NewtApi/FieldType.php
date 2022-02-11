<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

class FieldType
{
    const UNKNOWN  = 'unknown';
    const TEXT     = 'text';
    const HIDDEN   = 'hidden';
    const INTEGER  = 'integer';
    const DECIMAL  = 'decimal';
    const PASSWORD = 'password';
    const TEXTAREA = 'textarea';
    const CHECKBOX = 'checkbox';
    const DATE     = 'date';
    const TIME     = 'time';
    const DATETIME = 'datetime';
    const SELECT   = 'select';
    const LABEL    = 'label';
    const IMAGE    = 'image';
    const FILE     = 'file';
    const BARCODE  = 'barcode';
    const RATING   = 'rating';
}
