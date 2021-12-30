<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

interface EndpointInterface
{
    public function methodCreate(MethodCreateModel $model): bool;
    public function methodRead(MethodReadModel $model): array;
    public function methodUpdate(MethodUpdateModel $model): bool;
    public function methodDelete(MethodDeleteModel $model): bool;

    public function getAvailableMethodTypes(): array;
    public function getAvailableFields(): array;
}
