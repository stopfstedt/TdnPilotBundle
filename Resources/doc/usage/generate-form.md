[tdn:generate:form](generate-form.md)
===========================================
Generates [custom form type](http://symfony.com/doc/current/cookbook/form/create_custom_field_type.html) for a specified entity.

Usage
-----
```bash

$ ./bin/console tdn:generate:form [-t|--entity[="..."]] [-l|--entities-location[="..."]] [-o|--overwrite] [-d|--target-directory[="..."]]

```

#### Required (options)

You should only input one of these:

- entity: The entity to initialize in shortcut format (e.g. MyVendorFooBundle:MyEntity)
- entities-location: The relative (or absolute) path to your entities directory

#### Options
- overwrite: Overwrites existing files located in directory. **optional**
  <sub>Defaults to false.</sub>
- target-directory: Override the default output directory. **optional**
  <sub>Defaults to `<Bundle>/Form/Type/`.</sub>

In addition to this document, you can also pass in the `--help` flag for more information when running the command.

Dependencies
------------
* `<Bundle>/Entity/Manager/<Entity>Manager.php`

Output
------
By default the output directory will be `<Bundle>/Form/Type/`.

Files generated:

- `<Entity>Type.php`
- `InvalidFormException.php` (path is `<Bundle>/Exception`)

#### Example

Running:
`$ ./bin/console tdn:generate:form FooBarBundle:Foo`

Creates:
Form Type
``` php
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
            ->add('id')
            ->add('description')
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

```

Form Exception
```php
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

```
