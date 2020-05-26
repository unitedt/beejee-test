<?php

namespace App\ValueObject;

use App\Model\Task;

class TaskView
{
    /**
     * @var Task
     */
    private $task;

    /**
     * TaskView constructor.
     * @param Task $task
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Used for JSON result
     * @return array
     */
    public function getValues(): array
    {
        return [
            'id'       => $this->task->getId(),
            'userName' => htmlspecialchars($this->task->getUserName(), ENT_QUOTES, 'UTF-8'),
            'email'    => htmlspecialchars($this->task->getEmail(), ENT_QUOTES, 'UTF-8'),
            'content'  => htmlspecialchars($this->task->getContent(), ENT_QUOTES, 'UTF-8'),
            'isCompleted'   => $this->task->isCompleted() ? '<span class="badge badge-success">Completed</span>' : '',
            'isChangedByAdmin' => $this->task->isChangedByAdmin() ? '<span class="badge badge-warning">Changed by admin</span>' : '',
        ];
    }

}