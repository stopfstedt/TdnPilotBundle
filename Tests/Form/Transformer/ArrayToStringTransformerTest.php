<?php

namespace Tdn\PilotBundle\Tests\Form\Transformer;

use Tdn\PilotBundle\Form\DataTransformer\ArrayToStringTransformer;

/**
 * Class ArrayToStringTransformerTest
 * @package Tdn\PilotBundle\Tests\Form\Transformer
 */
class ArrayToStringTransformerTest extends AbstractTransformerTest
{
    /**
     * @return ArrayToStringTransformer
     */
    protected function getTransformer()
    {
        return new ArrayToStringTransformer();
    }

    /**
     * @return array
     */
    protected function getOriginal()
    {
        return ['foo', 'bar', 'baz'];
    }

    /**
     * @return string
     */
    protected function getTransformed()
    {
        return 'foo,bar,baz';
    }
}
