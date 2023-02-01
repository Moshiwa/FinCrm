<?php

namespace App\Traits;

use App\Services\Space\SpaceService;

trait ModelBaseConnectionTrait
{
    public function getConnectionName()
    {
        return SpaceService::getDefaultConnectionName();
    }
}
