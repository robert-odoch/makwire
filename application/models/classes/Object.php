<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Object
{
    private $id;
    private $type;
    private $ownerId;

    public function __construct($id, $type, $ownerId)
    {
        $this->id = $id;
        $this->type = $type;
        $this->ownerId = $ownerId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getType() {
        return $this->type;
    }

    public function getOwnerId() {
        return $this->ownerId;
    }
}
?>
