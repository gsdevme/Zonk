<?php

namespace Zonk;

use Symfony\Component\Yaml\Yaml;

class YmlConfigurationFactory
{
    /**
     * @param      $filePath
     * @param bool $singleTable
     *
     * @return Configuration
     */
    public function createFromYml($filePath, $singleTable = false)
    {
        if (!is_file($filePath)) {
            throw new \RuntimeException(sprintf('%s does not appear to exist', $filePath));
        }

        $config = Yaml::parse(file_get_contents($filePath));

        if ($singleTable !== false) {
            $configuration = new SingleTableConfiguration($config, $singleTable);
        } else {
            $configuration = new Configuration($config);
        }


        return $configuration;
    }
}
