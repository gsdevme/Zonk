<?php

namespace Zonk\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use Zonk\Configuration;

class CapsuleBuilder
{
    /**
     * @param Configuration $configuration
     *
     * @return Capsule
     */
    public function build(Configuration $configuration)
    {
        $capsule = new Capsule();

        if (!$configuration->hasConfigKey('database')) {
            throw new \RuntimeException(sprintf('No database configuration'));
        }

        $database = [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'database',
            'username'  => 'root',
            'password'  => 'password',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ];

        $database = array_merge($database, $configuration->getConfigKey('database'));

        $capsule->addConnection($database);

        // TODO this shouldn't be required
        $capsule->setAsGlobal();

        return $capsule;
    }
}
