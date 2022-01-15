<?php

declare(strict_types=1);

namespace Infonique\Newt\NewtApi;

interface EndpointInterface
{
    /**
     * Create new Item
     *
     * @param MethodCreateModel $model
     * @return Item
     */
    public function methodCreate(MethodCreateModel $model): Item;

    /**
     * Read Item
     *
     * @param MethodReadModel $model
     * @return Item
     */
    public function methodRead(MethodReadModel $model): Item;

    /**
     * Update Item
     *
     * @param MethodUpdateModel $model
     * @return Item
     */
    public function methodUpdate(MethodUpdateModel $model): Item;

    /**
     * Delete Item
     *
     * @param MethodDeleteModel $model
     * @return bool
     */
    public function methodDelete(MethodDeleteModel $model): bool;

    /**
     * List of Items
     *
     * @param MethodListModel $model
     * @return array<Item>
     */
    public function methodList(MethodListModel $model): array;


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
