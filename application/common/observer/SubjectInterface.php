<?php


namespace app\common\observer;


interface SubjectInterface{
    public function register(ObserverInterface $observer);
    public function notify();
}