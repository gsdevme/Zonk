<?php

namespace Zonk;

use Symfony\Component\Yaml\Yaml;

class YmlConfigurationFactory
{
    /**
     * @var array
     */
    private $requiredKeys;

    /**
     * YmlConfigurationFactory constructor.
     */
    public function __construct()
    {
        $this->requiredKeys = [
            'database' => [
                'username',
            ],
        ];
    }

    /**
     * @param $filePath
     *
     * @return Configuration
     */
    public function createFromYml($filePath)
    {
        if (!is_file($filePath)) {
            throw new \RuntimeException(sprintf('%s does not appear to exist', $filePath));
        }

        $config = Yaml::parse(file_get_contents($filePath));
        $this->validate($config);

        $configuration = new Configuration($config);

        return $configuration;
    }

    /**
     * @TODO
     */
    private function validate($config)
    {
        return true;
    }
}
