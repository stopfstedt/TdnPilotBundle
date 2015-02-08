<?php

namespace Tdn\PilotBundle\Tests\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use Tdn\PilotBundle\Form\Type\ManyRelatedType;

/**
 * Class ManyRelatedTypeTest
 * @package Tdn\PilotBundle\Tests\Form\Transformer
 */
class ManyRelatedTypeTest extends AbstractRelatedTypeTest
{
    /**
     * @return \stdClass
     */
    protected function getEntity()
    {
        $object = new \stdClass();
        $object->id = 1;

        return $object;
    }

    public function testSubmitValidData()
    {
        $formData = [
            '1',
            '1',
            '1'
        ];

        $type = new ManyRelatedType($this->getEntityManager());
        $form = $this->factory->create($type, null, ['entityName' => 'FooBarBundle:Bar']);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals(
            new ArrayCollection([
                $this->getEntity(),
                $this->getEntity(),
                $this->getEntity()
            ]),
            $form->getData()
        );
    }
}
