<?php

namespace Tdn\PilotBundle\Tests\Form\Transformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Tdn\PilotBundle\Form\DataTransformer\ManyRelatedTransformer;

/**
 * Class ManyRelatedTransformerTest
 * @package Tdn\PilotBundle\Tests\Form\Transformer
 */
class ManyRelatedTransformerTest extends AbstractTransformerTest
{
    /**
     * @return DataTransformerInterface
     */
    protected function getTransformer()
    {
        return new ManyRelatedTransformer($this->getEntityManager(), $this->getEntity()->name);
    }

    /**
     * @return ArrayCollection
     */
    protected function getOriginal()
    {
        return new ArrayCollection([
            $this->getEntity()
        ]);
    }

    /**
     * @return string[]
     */
    protected function getTransformed()
    {
        return ['1'];
    }
}
