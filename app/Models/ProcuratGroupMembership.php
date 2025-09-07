<?php

namespace App\Models;

use stdClass;

class ProcuratGroupMembership
{
    private int $id;
    private int $groupId;
    private int $personId;
    private stdClass $data;

    /**
     * @param int $id
     * @param int $groupId
     * @param int $personId
     * @param stdClass $data
     */
    public function __construct(int $id, int $groupId, int $personId, stdClass $data)
    {
        $this->id = $id;
        $this->groupId = $groupId;
        $this->personId = $personId;
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @return int
     */
    public function getPersonId(): int
    {
        return $this->personId;
    }

    /**
     * @return stdClass
     */
    public function getData(): stdClass
    {
        return $this->data;
    }
}