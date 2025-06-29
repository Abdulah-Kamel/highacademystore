<?php

namespace App\Enums;

enum ProductEnum:string {
    case percentage = 'percentage';
    case value = 'value';

    public function signs(): string
    {
        return match($this){
            self::percentage => '%',
            self::value =>'L.E',
        };
    }
}
