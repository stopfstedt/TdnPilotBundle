<?php

namespace Tdn\PilotBundle\Tests\Form\Transformer;

use Tdn\PilotBundle\Form\DataTransformer\SingleRelatedTransformer;

/**
 * Class SingleRelatedTransformerTest
 * @package Tdn\PilotBundle\Tests\Form\Transformer
 */
class SingleRelatedTransformerTest extends AbstractTransformerTest
{
    /**
     * @return SingleRelatedTransformer
     */
    protected function getTransformer()
    {
        return new SingleRelatedTransformer($this->getEntityManager(), $this->getEntity()->name);
    }

    /**
     * @return \stdClass
     */
    protected function getOriginal()
    {
        return $this->getEntity();
    }

    /**
     * @return string
     */
    protected function getTransformed()
    {
        return '1';
    }
}
