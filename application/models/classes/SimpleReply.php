<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('Object.php');
require_once(dirname(__FILE__) . '/../interfaces/Likeable.php');

class SimpleReply extends Object implements Likeable
{
    public function __construct($id, $ownerId, $type = 'reply')
    {
        parent::__construct($id, $ownerId, $type);
    }
}
?>
