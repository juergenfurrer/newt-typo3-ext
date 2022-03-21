<?php

declare(strict_types=1);

return [
    \Infonique\Newt\Domain\Model\FileReference::class => [
        'tableName' => 'sys_file_reference'
    ],
    \Infonique\Newt\Domain\Model\FrontendUser::class => [
        'tableName' => 'fe_users',
    ],
    \Infonique\Newt\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users',
    ],
];
