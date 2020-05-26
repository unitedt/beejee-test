<?php

namespace App\Repository;

use App\Model\Task;
use App\ValueObject\TaskSorting;
use Doctrine\DBAL\Connection;
use Psr\Container\ContainerInterface;

class TaskRepository implements TaskRepositoryInterface
{
    public const TABLE_NAME = 'task';

    /**
     * @var Connection
     */
    private $conn;

    /**
     * TaskRepository constructor.
     * @param Connection $conn
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @param Task $task
     * @return Task
     */
    public function save(Task $task): Task
    {
        if (!empty($task->getId())) {
            return $this->update($task);
        } else {
            return $this->insert($task);
        }
    }

    public function update(Task $task): Task {
        $qb = $this->conn->createQueryBuilder();

        $qb->update(self::TABLE_NAME);

        $qb->set('userName', ':userName');
        $qb->set('email', ':email');
        $qb->set('content', ':content');
        $qb->set('isCompleted', ':isCompleted');
        $qb->set('isChangedByAdmin', ':isChangedByAdmin');

        $qb->where('id = :id');

        $qb->setParameter(':userName', $task->getUserName());
        $qb->setParameter(':email', $task->getEmail());
        $qb->setParameter(':content', $task->getContent());
        $qb->setParameter(':isCompleted', (int)$task->isCompleted());
        $qb->setParameter(':isChangedByAdmin', (int)$task->isChangedByAdmin());
        $qb->setParameter(':id', $task->getId());

        $qb->execute();

        return $task;
    }

    public function insert(Task $task): Task {
        $qb = $this->conn->createQueryBuilder();

        $qb
            ->insert(self::TABLE_NAME)
            ->values([
                'userName' => ':userName',
                'email' => ':email',
                'content' => ':content',
                'isCompleted' => ':isCompleted',
                'isChangedByAdmin' => ':isChangedByAdmin',
            ]);

        $qb->setParameter(':userName', $task->getUserName());
        $qb->setParameter(':email', $task->getEmail());
        $qb->setParameter(':content', $task->getContent());
        $qb->setParameter(':isCompleted', (int)$task->isCompleted());
        $qb->setParameter(':isChangedByAdmin', (int)$task->isChangedByAdmin());

        if ($qb->execute() > 0) {
            $task->setId($this->conn->lastInsertId());
        }

        return $task;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        $qb = $this->conn->createQueryBuilder();

        $qb->select('COUNT(*)')
           ->from(self::TABLE_NAME)
        ;

        return $qb->execute()->fetchColumn();
    }

    /**
     * @param TaskSorting $sorting
     * @param int $offset
     * @param int $limit
     * @return array
     */
    public function getAll(TaskSorting $sorting, $offset = 0, $limit = 10): array
    {
        $qb = $this->conn->createQueryBuilder();

        $qb->select('*')
           ->from(self::TABLE_NAME)
        ;

        $qb->orderBy($sorting->getSort(), $sorting->getOrder());

        $qb->setFirstResult($offset);
        $qb->setMaxResults($limit);

        $stmt = $qb->execute();

        $ret = [];

        while ($row = $stmt->fetch()) {
            $ret[] = $this->hydrate($row);
        }

        return $ret;
    }

    /**
     * @param int $id
     * @return Task|null
     * @throws \OutOfBoundsException
     */
    public function findById(int $id): Task
    {
        $qb = $this->conn->createQueryBuilder();

        $qb->select('*')
            ->from(self::TABLE_NAME)
            ->where('id = :id')
            ->setParameter(':id', $id)
        ;

        $stmt = $qb->execute();

        if (!($row = $stmt->fetch())) {
            throw new \OutOfBoundsException('No task with such id: ' . $id);
        }

        return $this->hydrate($row);
    }

    /**
     * Transfers row array from DB into Task object
     * @param array $row
     * @return Task
     */
    private function hydrate(array $row): Task
    {
        $task = new Task();
        $task->setId($row['id'])
             ->setUserName($row['userName'])
             ->setEmail($row['email'])
             ->setContent($row['content'])
             ->setIsCompleted($row['isCompleted'])
             ->setIsChangedByAdmin($row['isChangedByAdmin'])
        ;
        return $task;
    }

}