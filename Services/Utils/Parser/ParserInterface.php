<?php

namespace Tdn\PilotBundle\Services\Utils\Parser;

/**
 * Interface ParserInterface
 * @package Tdn\PilotBundle\Services\Utils\Parser
 */
interface ParserInterface
{
    /**
     * @param string $contents
     *
     * @return array
     */
    public function getDefinitions($contents);
}
