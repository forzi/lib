<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 11.10.2016
 * Time: 21:24
 */

namespace stradivari\event;

use stradivari\event\interfaces\IEvent;

class Event implements IEvent {
    protected $sender;
    protected $data;

    public function send($sender = null, $data = null) {
        $this->sender = $sender;
        $this->data = $data;
        EventMediator::getInstance()->getEvent($this);
    }

    public function getSender() {
        return $this->sender;
    }

    public function getData() {
        return $this->data;
    }
}