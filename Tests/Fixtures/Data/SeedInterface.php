<?php

namespace Tdn\PilotBundle\Tests\Fixtures\Data;

/**
 * Data files should contain an array structure that would
 * match the expected json when converted with json_encode.
 *
 * Interface SeedInterface
 * @package Tdn\PilotBundle\Tests\Fixtures\Data
 */
interface SeedInterface
{
    /**
     * @return array
     */
    public function getAll();

    /**
     * @return array
     */
    public function getOne();

    /**
     * @return array
     */
    public function create();

    /**
     * @return array
     */
    public function createWithId();

    /**
     * @return array
     */
    public function invalid();
}
