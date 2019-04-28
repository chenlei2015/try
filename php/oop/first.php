<?php

namespace php\oop;

class first{

    public function __construct()
    {
        $this->who();
    }

    public function identity(){
        $this->who();
    }

    public function who(){

        echo 'first';
    }

}
