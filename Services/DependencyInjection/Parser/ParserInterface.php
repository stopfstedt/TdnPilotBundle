<?php

namespace Tdn\PilotBundle\Services\DependencyInjection\Parser;

/**
 * Interface ParserInterface
 * @package Tdn\PilotBundle\Services\DependencyInjection\Parser
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
