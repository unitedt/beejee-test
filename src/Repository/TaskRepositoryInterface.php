<?php

namespace App\Repository;

use App\Model\Task;
use App\ValueObject\TaskSorting;

interface TaskRepositoryInterface
{
    /**
     * @param Task $task
     * @return Task
     */
    public function save(Task $task): Task;

    /**
     * @return int
     */
    public function count(): int;

    /**
     * @param TaskSorting $sorting
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getAll(TaskSorting $sorting, $offset = 0, $limit = 10): array;

    /**
     * @param int $id
     * @return Task
     */
    public function findById(int $id): Task;
}