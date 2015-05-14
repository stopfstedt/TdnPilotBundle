<?php

namespace Tdn\PilotBundle\Tests\Fixtures\Data;

/**
 * Data files should contain an array structure that would
 * match the expected json when converted with json_encode.
 *
 * Interface DataInterface
 * @package Tdn\PilotBundle\Tests\Fixtures\Data
 */
interface DataInterface
{
    /**
     * @return array
     */
    public function cget();

    /**
     * @return array
     */
    public function post();

    /**
     * @return array
     */
    public function badPost();

    /**
     * @return array
     */
    public function put();
}
