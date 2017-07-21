<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once('Object.php');
require_once(dirname(__FILE__) . '/../interfaces/Likeable.php');
require_once(dirname(__FILE__) . '/../interfaces/Shareable.php');
require_once(dirname(__FILE__) . '/../interfaces/Commentable.php');

class SimpleVideo extends Object implements Likeable, Shareable, Commentable
{
    public function __construct($id, $type, $ownerId)
    {
        parent::__construct($id, $type, $ownerId);
    }
}
?>
