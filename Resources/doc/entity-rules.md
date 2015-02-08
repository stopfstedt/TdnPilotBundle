Valid Entity
------------
To guarantee that this bundle will work, entities must follow these basic guidelines:

- Proper setters/getters
- Proper docblock
- Proper annotations (orm, odm, symfony constraints, etc)

Type rules:
- Collections
    - Should be annotated in docblocks: `<CollectionType>|ObjectType[]`
- Booleans
    - Getters should be named: `is|has|should|may|supports<VarName>()`
- Others
    - Setters should be named: `set<VarName>`
    - Getters should be named: `get<VarName>`

Behavioral methods will not be tested, but a stub will be added.

If your object has dependencies, it must be declared as a service.
The entity service name must follow this convention: `<bundle-name>.entity.<entity-name>`

Most basic example below:
``` php
<?php

namespace Foo\Bar;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

class Abc implements AbcInterface
{
    /**
     * @var string
     */
    protected $foo;

    /**
     * @var bool
     */
    protected $bar;

    /**
     * @var ArrayCollection|ExtraInterface[]
     */
    protected $extras;

    /**
     * @param string $foo
     */
    public function setFoo($foo)
    {
        $this->foo = $foo;
    }

    /**
     * @return string
     */
    public function getFoo()
    {
        return $this->foo;
    }

    /**
     * @param bool $bar
     */
    public function setBar($bar)
    {
        $this->bar = $bar;
    }

    /**
     * Alternatively: isBar()
     * @return bool
     */
    public function hasBar()
    {
        return $this->bar;
    }

    /**
     * @param Collection $extras
     */
    public function setExtras(Collection $extras)
    {
        $this->extras = new ArrayCollection();
        foreach ($extras as $extra) {
            $this->addExtra($extra);
        }
    }

    /**
     * @param ExtraInterface $extra
     */
    public function addExtra(ExtraInterface $extra)
    {
        $this->extras->add($extra);
    }

    /**
     * @return ArrayCollection|ExtraInterface[]
     */
    public function getExtras()
    {
        return $this->extras;
    }
}

```
