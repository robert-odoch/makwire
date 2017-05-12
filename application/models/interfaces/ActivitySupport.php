<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface ActivitySupport
{
    public function getId();
    public function getType();
    public function getOwnerId();
}
?>
