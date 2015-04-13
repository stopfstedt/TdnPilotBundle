<?php

namespace Tdn\PilotBundle\Services\Utils\Symfony;

use Symfony\Component\Finder\SplFileInfo;
use Tdn\PilotBundle\Model\Format;

/**
 * Class AbstractFileUtils
 * @package Tdn\PilotBundle\Services\Utils\Symfony
 */
abstract class AbstractFileUtils
{
    /**
     * @return array
     */
    public static function getSupportedExtensions()
    {
        return [
            Format::XML,
            Format::YML,
            Format::YAML
        ];
    }

    /**
     * @param SplFileInfo $file
     * @throws \InvalidArgumentException when file is not a supported format.
     *
     * @return string
     */
    protected function getFormat(SplFileInfo $file)
    {
        if (!in_array(strtolower($file->getExtension()), self::getSupportedExtensions())) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Invalid format. Expected one of %s, got %s.',
                    implode(',', self::getSupportedExtensions()),
                    $file->getExtension()
                )
            );
        }

        return strtolower($file->getExtension());
    }
}
