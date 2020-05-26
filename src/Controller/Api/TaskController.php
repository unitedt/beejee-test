<?php

namespace App\Controller\Api;

use App\Repository\TaskRepositoryInterface;
use App\ValueObject\TaskSorting;
use App\Model\Task;
use App\ValueObject\TaskView;
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
        $tasks = $this->repository->getAll($sorting, $_request->query->get('offset', 0), $_request->query->get('limit', 3));

        $ret = [
            'total' => $this->repository->count(),
            'rows' =>  array_map(function(Task $task) { return (new TaskView($task))->getValues(); }, $tasks),
        ];

        return json_encode($ret);
    }

}