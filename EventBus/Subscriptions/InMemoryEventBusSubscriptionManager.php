<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 24/08/2017
 * Time: 21:31
 */

namespace JPuminate\Contracts\EventBus\Subscriptions;


use JPuminate\Contracts\EventBus\Events\IntegrationEvent;
use ReflectionClass;

class InMemoryEventBusSubscriptionManager implements SubscriptionManager
{

    private $handlers;

    private $events;

    private $subscription_key;

    public function __construct()
    {
        $this->handlers = [];
        $this->events = [];
        $this->subscription_key = $this->gen_uuid();
    }

    public function isEmpty()
    {
        return count($this->handlers) == 0;
    }

    public function addSubscription($event, $handler)
    {
       if(!$this->hasSubscriptionsForEvent($event)){
           $this->handlers[$event] = [];
       }
       if(in_array($handler, $this->handlers[$event])){
           throw new \InvalidArgumentException(sprintf("Handler Type %s already registered for '%s'", $handler, $event));
       }
        array_push($this->handlers[$event], $handler);
        $this->events[$this->getEventKey($event)] = $event;
    }

    public function removeSubscription($event, $handler)
    {
        if(!array_key_exists($event, $this->handlers)) return;
        if(($key = array_search($handler, $this->handlers[$event])) !== false) {
            unset($this->handlers[$event][$key]);
        }
        if(!count($this->handlers[$event])) {
            unset($this->handlers[$event]);
            unset($this->events[$this->getEventKey($event)]);
        }
    }

    public function hasSubscriptionsForEvent($event)
    {
        return array_key_exists($event, $this->handlers);
    }


    public function clear()
    {
         unset($this->handlers);
         $this->handlers = [];
    }

    public function getHandlersForEvent($event)
    {
        return $this->handlers[$event];
    }

    public function getEventKey($event)
    {
        if(is_string($event)){
            $arr = explode('\\', $event);
            return strtolower($arr[count($arr) -1]);
        }
        else if($event instanceof IntegrationEvent){
            $reflect = new ReflectionClass($event);
            return strtolower($reflect->getShortName());
        }
       throw new \Exception("unsupported event type");
    }


    public function getEventTypeFromKey($event_key)
    {
        return $this->events[$event_key];
    }

    public function getSubscriptionKey()
    {
        return $this->subscription_key;
    }

    private function gen_uuid()
    {
        return sprintf('%04x%04x-%04x-%04x',
            // 32 bits for "time_low"
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),

            // 16 bits for "time_mid"
            mt_rand(0, 0xffff),

            mt_rand( 0, 0x0fff ) | 0x4000
        );
    }
}