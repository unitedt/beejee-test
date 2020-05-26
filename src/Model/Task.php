<?php

namespace App\Model;


class Task
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $content;

    /**
     * @var bool
     */
    private $isCompleted = false;

    /**
     * @var bool
     */
    private $isChangedByAdmin = false;

    /**
     * Used for JSON result
     * @return array
     */
    public function getViewValue(): array
    {
        return [
            'id'       => $this->id,
            'userName' => htmlspecialchars($this->userName, ENT_QUOTES, 'UTF-8'),
            'email'    => htmlspecialchars($this->email, ENT_QUOTES, 'UTF-8'),
            'content'  => htmlspecialchars($this->content, ENT_QUOTES, 'UTF-8'),
            'isCompleted'   => $this->isCompleted,
            'isChangedByAdmin' => $this->isChangedByAdmin,
        ];
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Task
     */
    public function setId(int $id): Task
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getUserName(): ?string
    {
        return $this->userName;
    }

    /**
     * @param string $userName
     * @return Task
     */
    public function setUserName(string $userName): Task
    {
        $this->userName = $userName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Task
     */
    public function setEmail(string $email): Task
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getContent(): ?string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return Task
     */
    public function setContent(string $content): Task
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->isCompleted;
    }

    /**
     * @param int $isCompleted
     * @return Task
     */
    public function setIsCompleted(bool $isCompleted): Task
    {
        $this->isCompleted = $isCompleted;
        return $this;
    }

    /**
     * @return bool
     */
    public function isChangedByAdmin(): bool
    {
        return $this->isChangedByAdmin;
    }

    /**
     * @param bool $isChangedByAdmin
     * @return Task
     */
    public function setIsChangedByAdmin(bool $isChangedByAdmin): Task
    {
        $this->isChangedByAdmin = $isChangedByAdmin;
        return $this;
    }

}