<?php

namespace Tdn\PilotBundle\Tests\Fixtures;

/**
 * Static data fixture for form tests.
 *
 * Class FormData
 * @package Tdn\PilotBundle\Tests\Fixtures
 */
class FormData
{
    const FORM_EXCEPTION = <<<'FORM_EXCEPTION'
<?php

namespace Foo\BarBundle\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class InvalidFormException
 * @package Foo\BarBundle\Exception
 */
class InvalidFormException extends BadRequestHttpException
{
    protected $form;

    public function __construct($message, $form = null)
    {
        parent::__construct($message);
        $this->form = $form;
    }

    public function getForm()
    {
        return $this->form;
    }
}

FORM_EXCEPTION;

    const FOO_FORM_TYPE = <<<'FOO_FORM_TYPE'
<?php

namespace Foo\BarBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FooType
 * @package Foo\BarBundle\Form\Type
 */
class FooType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description')
            ->add('name')
            ->add('title')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Foo\BarBundle\Entity\Foo'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'foo_type';
    }
}

FOO_FORM_TYPE;

}
