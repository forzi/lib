<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 11.10.2016
 * Time: 15:39
 */

namespace stradivari\event\interfaces;

interface ISubscriber {
    public function getEvent(IEvent $event);
}
