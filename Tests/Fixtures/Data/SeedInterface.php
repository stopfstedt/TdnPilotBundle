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
    public function cget();

    /**
     * @return array
     */
    public function post();

    /**
     * @return array
     */
    public function badPosts();

    /**
     * @return array
     */
    public function put();
}
