<?php

namespace App\ValueObject;

class TaskSorting
{
    private const SORT = ['userName', 'email', 'isCompleted'];
    private const ORDER = ['asc', 'desc'];

    /**
     * @var string
     */
    private $sort;

    /**
     * @var string
     */
    private $order;

    /**
     * TaskSorting constructor.
     * @param string $sort
     * @param string $order
     * @throws \InvalidArgumentException
     */
    public function __construct(string $sort, string $order)
    {
        if (!\in_array($sort, self::SORT, 1)) {
            throw new \InvalidArgumentException('Invalid field to sort: ' . $sort);
        }

        if (!\in_array($order, self::ORDER, 1)) {
            throw new \InvalidArgumentException('Invalid order to sort: ' . $order);
        }

        $this->sort = $sort;
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function getSort(): string
    {
        return $this->sort;
    }

    /**
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

}