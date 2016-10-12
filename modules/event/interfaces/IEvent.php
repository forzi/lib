<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 11.10.2016
 * Time: 15:38
 */

namespace stradivari\event\interfaces;

interface IEvent {
    public function send($sender = null, $data = null);
    public function getSender();
    public function getData();
}
