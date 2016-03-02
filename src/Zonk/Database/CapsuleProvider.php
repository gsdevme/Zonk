<?php

namespace Zonk\Database;

use Illuminate\Database\Capsule\Manager as Capsule;

class CapsuleProvider
{
    private $capsule;

    public function __construct(Capsule $capsule)
    {
        $this->capsule = $capsule;
    }

    public function getCapsule()
    {
        return $this->capsule;
    }
}
