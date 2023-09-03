<?php

declare(strict_types=1);

namespace Swisscode\Newt\NewtApi;

class MethodType
{
    const UNKNOWN = 'unknown';
    const CREATE  = 'create';
    const READ    = 'read';
    const UPDATE  = 'update';
    const DELETE  = 'delete';
    const LIST    = 'list';
}
