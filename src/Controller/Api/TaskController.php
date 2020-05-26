<?php

namespace App\Controller\Api;

use App\Repository\TaskRepositoryInterface;
use App\ValueObject\TaskSorting;
use App\Model\Task;
use Symfony\Component\HttpFoundation\Request;

class TaskController
{
    /**
     * @var TaskRepositoryInterface
     */
    private $repository;

    public function __construct(TaskRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function __invoke(Request $_request)
    {
        $sorting = new TaskSorting($_request->query->get('sort', 'userName'), $_request->query->get('order', 'asc'));
        $tasks = $this->repository->getAll($sorting, $_request->query->get('offset', 0), $_request->query->get('limit', 10));

        $ret = [
            'total' => $this->repository->count(),
            'rows' =>  array_map(function(Task $task) { return $task->getViewValue(); }, $tasks),
        ];

        return json_encode($ret);
    }

}