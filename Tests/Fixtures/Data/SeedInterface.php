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

    /**
     * @return array
     */
    public function getLastCreated();

    /**
     * Array of strings containing the property names that should remain private.
     *
     * @return array
     */
    public function getPrivateFields();
}
