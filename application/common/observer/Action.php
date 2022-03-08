<?php


namespace app\common\observer;


class Action implements SubjectInterface{
    public $_observers=[];

    public function register(ObserverInterface $observer){
        $this->_observers[] = $observer;
    }

    public function notify(){
        foreach ($this->_observers as $observer){
            $observer->watch();
        }
    }
}