<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('Object.php');
require_once(dirname(__FILE__) . '/../interfaces/Likeable.php');
require_once(dirname(__FILE__) . '/../interfaces/Replyable.php');

class SimpleBirthdayMessage extends Object implements Likeable, Replyable
{
    public function __construct($id, $ownerId, $type = 'birthday_message')
    {
        parent::__construct($id, $ownerId, $type);
    }
}
?>
