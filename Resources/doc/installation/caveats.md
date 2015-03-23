Directory Structure
-------------------
Bundles following the [symfony2 best practices] should already meet the expectations here.

The following namespace structure is expected for bundles:

- Controllers: `<BundleNamespace>\Controllers`
- Form Types: `<BundleNamespace>\Form\Type`
- Rest Handler: `<BundleNamespace>\Handler`
- Entities: `<BundleNamespace>\Entity`
- Entity Manager: `<BundleNamespace>\Entity\Manager`

Resource Directory:

- Bundle Config: `<BundleRoot>/Resources/config`

Entity Gotchas
--------------
To guarantee that this bundle will work, entities must follow these basic guidelines:

- Proper setters/getters
- Proper docblock
- Proper annotations (orm, odm, symfony constraints, etc)

Type rules:

- Collections
    - Should be annotated in docblocks `<CollectionType>|ObjectTypeInterface[]`
    - Methods: `set<CollectionPlural>(Collection $collection)`, `add<CollectionSingular>(SomeInterface $obj)`,
    `remove<CollectionSingluar>(SomeInterface $obj)`, and `get<CollectionPlural>()` should be present when dealing with
    collections.
- Booleans
    - Getters should be named: `is|has|should|may|supports<VarName>()`
- Arrays
    - should be annotated `array` or if possible to be more descriptive `<type>[]` (e.g.  `string[]`)
- OneToMany/ManyToMany
    - Should be held in `Collection` **not** in arrays*
- Everything else
    - Setters should be named: `set<VarName>`
    - Getters should be named: `get<VarName>`
    - Should be annotated accordingly (e.g. `@param int $myInt`, `@return int`, etc)

Behavioral methods will not be tested but would be welcomed in a PR if even possible (through AST or otherwise).

If your object has service dependencies, it SHOULD be declared as a service (you should also consider splitting your
 object into an entity and a service object or business object).*
The entity service name SHOULD follow this convention: `<bundle-name>.entity.<entity-name>` *

`*` Note: This is not currently needed but will be in the next versions of the bundle.

#### slug

It is recommended that if using slugs for 

Most basic example below:
``` php
<?php

namespace Foo\Bar\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Type;

/**
 * Class Abc
 * @package Foo\BarBundle\Entity
 *
 * @ORM\Table(name="abc")
 * @ORM\Entity
 *
 * @ExclusionPolicy("all")
 */
class Abc implements AbcInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Assert\Type(type="integer")
     *
     * @Expose
     * @Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=90)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     *
     * @Expose
     * @Type("string")
     */
    protected $foo;

    /**
     * @var bool
     *
     * @ORM\Column(name="bar", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @Expose
     * @Type("boolean")
     */
    protected $bar;

    /**
     * @var XyzInterface
     *
     * @ORM\ManyToOne(targetEntity="Xyz", inversedBy="abcs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="xyz_id", referencedColumnName="id")
     * })
     *
     * @Expose
     * @Type("Foo\BarBundle\Entity\Xyz")
     */
    protected $xyz;

    /**
     * @var ArrayCollection|ExtraInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Extra", inversedBy="abcs")
     * @ORM\JoinTable(name="acbs_extras",
     *   joinColumns={
     *     @ORM\JoinColumn(name="abc_id", referencedColumnName="id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="extra_id", referencedColumnName="id")
     *   }
     * )
     *
     * @Expose
     * @Type("ArrayCollection<Foo\BarBundle\Entity\Extra>")
     */
    protected $extras;

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

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
     * Alternatively: is|has|should|may|supports<Bar>()
     * @return bool
     */
    public function hasBar()
    {
        return $this->bar;
    }

    /**
     * @param XyzInterface|null $xyz
     */
    public function setXyz(XyzInterface $xyz = null)
    {
        $this->xyz = $xyz;
    }

    /**
     * @return XzyInterface
     */
    public function getXyz()
    {
        return $this->xyz;
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
     * @param ExtraInterface $extra
     */
    public function removeExtra(ExtraInterface $extra)
    {
        $this->extras->removeElement($extra);
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

[symfony2 best practices]: http://symfony.com/doc/current/best_practices/index.html
[doctrine documentation for removing associations]: http://doctrine-orm.readthedocs.org/en/latest/reference/working-with-associations.html
