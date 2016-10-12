<?php
/**
 * Created by PhpStorm.
 * User: forzi
 * Date: 11.10.2016
 * Time: 21:33
 */

namespace stradivari\event;

use stradivari\event\interfaces\IEvent;
use stradivari\event\interfaces\ISubscriber;
use stradivari\singleton\TSingleton;

final class EventMediator implements ISubscriber {
    use TSingleton;

    protected $subscribers = [];

    public function subscribe(ISubscriber $subscriber, $eventClass = 'all') {
        $this->subscribers[$eventClass][spl_object_hash($subscriber)] = $subscriber;
    }

    public function unsubscribe(ISubscriber $subscriber, $eventClass = 'all') {
        unset($this->subscribers[$eventClass][spl_object_hash($subscriber)]);
        if ($eventClass != 'all') {
            return;
        }
        foreach ($this->subscribers as $key => $value) {
            if ($key != 'all') {
                $this->unsubscribe($subscriber, $key);
            }
        }
    }

    public function getEvent(IEvent $event) {
        $subscribers = $this->subscribers($event);
        foreach ($subscribers as $subscriber) {
            $subscriber->getEvent($event);
        }
    }

    protected function subscribers(IEvent $event) {
        $list = [];
        foreach ($this->subscribers as $key => $subscribers) {
            if ($key == 'all' || $event instanceof $key) {
                $list = array_merge($list, $subscribers);
            }
        }
        return $list;
    }

}
