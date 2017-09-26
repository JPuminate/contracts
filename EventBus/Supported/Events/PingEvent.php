<?php
/**
 * Created by PhpStorm.
 * User: Ouachhal
 * Date: 29/08/2017
 * Time: 16:29
 */
namespace JPuminate\Contracts\EventBus\Supported\Events;

use Exceptions\Data\TypeException;
use JPuminate\Contracts\EventBus\Events\Event;

class PingEvent extends Event
{
     public static function deserialize($object): Event
     {
         $event = new static();
         try {
             foreach ($object as $key => $value) {
                 $event->{$key} = $value;
             }
             return $event;
         } catch (\Exception $e) {
             throw new TypeException("cannot deserialize this object");
         }
     }
}