<?php

declare(strict_types=1);

return [
    \Swisscode\Newt\Domain\Model\FileReference::class => [
        'tableName' => 'sys_file_reference'
    ],
    \Swisscode\Newt\Domain\Model\FrontendUser::class => [
        'tableName' => 'fe_users',
    ],
    \Swisscode\Newt\Domain\Model\BackendUser::class => [
        'tableName' => 'be_users',
    ],
];
