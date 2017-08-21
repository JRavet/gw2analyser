<?php
/*
id int(11) unsigned AUTO_INCREMENT,
name varchar(64),
password varchar(41),
email varchar(64),
PRIMARY KEY (id)
*/
class user extends TinyMVC_Model
{
	protected $_table = "user";
	protected $pk = "id";
}

?>