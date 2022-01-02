<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

interface EndpointInterface
{
    /**
     * Create new Item
     *
     * @param MethodCreateModel $model
     * @return bool
     */
    public function methodCreate(MethodCreateModel $model): bool;

    /**
     * List of items
     *
     * @param MethodReadModel $model
     * @return array<Item>
     */
    public function methodRead(MethodReadModel $model): array;

    /**
     * Update Item
     *
     * @param MethodUpdateModel $model
     * @return bool
     */
    public function methodUpdate(MethodUpdateModel $model): bool;

    /**
     * Delete Item
     *
     * @param MethodDeleteModel $model
     * @return bool
     */
    public function methodDelete(MethodDeleteModel $model): bool;


    /**
     * Returns the implemented methods
     *
     * @param MethodDeleteModel $model
     * @return array<MethodType>
     */
    public function getAvailableMethodTypes(): array;

    /**
     * Returns the fields used in this endpoint
     *
     * @param MethodDeleteModel $model
     * @return array<Field>
     */
    public function getAvailableFields(): array;
}
