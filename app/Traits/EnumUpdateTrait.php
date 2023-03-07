<?php


namespace App\Traits;

trait EnumUpdateTrait
{
    public static function fromValue(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if( $name === $case->value ){
                return $case;
            }
        }

        return null;
    }
}
