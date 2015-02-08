<?php

namespace Tdn\PilotBundle\Tests\Form\Type;

use Tdn\PilotBundle\Form\Type\SingleRelatedType;

/**
 * Class SingleRelatedTypeTest
 * @package Tdn\PilotBundle\Tests\Form\Type
 */
class SingleRelatedTypeTest extends AbstractRelatedTypeTest
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
            '1'
        ];

        $type = new SingleRelatedType($this->getEntityManager());
        $form = $this->factory->create($type, null, ['entityName' => 'FooBarBundle:Bar']);

        $form->submit($formData);
        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($this->getEntity(), $form->getData());
    }
}
