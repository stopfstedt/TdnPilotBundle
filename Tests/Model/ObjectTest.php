<?php
namespace Tdn\SfProjectGeneratorBundle\Tests\Model;

use Symfony\Component\Finder\SplFileInfo;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Instantiator\Instantiator;
use JMS\PhpManipulator\TokenStream;
use \Mockery;

use Tdn\SfProjectGeneratorBundle\Manipulator\Object\MethodManipulator;
use Tdn\SfProjectGeneratorBundle\Manipulator\Object\PropertyManipulator;
use Tdn\SfProjectGeneratorBundle\Manipulator\ObjectManipulator;
use Tdn\SfProjectGeneratorBundle\Tests\Model\FileInterface;
use Tdn\SfProjectGeneratorBundle\Model\Source;
use Tdn\SfProjectGeneratorBundle\Model\Method;
use Tdn\SfProjectGeneratorBundle\Model\Object;
use Tdn\SfProjectGeneratorBundle\Model\Property;
use Tdn\PhpTypes\Type\String;

/**
 * Class ObjectTest
 * @package Tdn\SfProjectGeneratorBundle\Tests\Model
 */
class ObjectTest extends \PHPUnit_Framework_TestCase implements FileInterface
{
    /**
     * @var \Tdn\SfProjectGeneratorBundle\Model\Object
     */
    protected $object;

    protected function setUp()
    {
        $fileMock = Mockery::mock((new Instantiator())->instantiate('Symfony\\Component\\Finder\\SplFileInfo'));
        $fileMock
            ->shouldReceive(['getBasename' => 'VirtualClass', 'getContents' => self::FILE_CONTENTS])
            ->atLeast(1);

        /** @var TokenStream $stream */
        $stream = new TokenStream();
        $stream->setIgnoreComments(false);
        $stream->setCode(self::FILE_CONTENTS);

        /** @var Source $source */
        $source = (new Instantiator())->instantiate('Tdn\\SfProjectGeneratorBundle\\Model\\Source');
        $source->setStream($stream);
        $source = Mockery::mock($source);
        $source
            ->shouldReceive('getFile')
            ->atLeast(1)
            ->andReturn($fileMock);

        $this->object = new Object(new ObjectManipulator($source));
    }

    public function testType()
    {
        $this->assertEquals(T_CLASS, $this->object->getType());
    }

    public function testNameSpace()
    {
        $this->assertEquals(
            'Tdn\SfProjectGeneratorBundle\Test\Model\Virtual',
            (string) $this->object->getNamespace()
        );
    }

    public function testUseLines()
    {
        $useLines = new ArrayCollection(['\StdClass', '\SplFileInfo']);
        $this->assertEquals($useLines, $this->object->getUseLines());
    }

    public function testDocBlock()
    {
        $this->assertEquals("/**\n * @author Victor Passapera\n */", (string) $this->object->getDocBlock());
    }

    public function testName()
    {
        $message = 'Function logic: ';
        $this->assertEquals(
            'VirtualClass',
            (string) $this->object->getName(true),
            $message . 'Using FileName = true.'
        ); //Clearly faster.
        $this->assertEquals(
            'VirtualClass',
            (string) $this->object->getName(),
            $message . 'Using FileName = false; Using Stream = true.'
        );
    }

    public function testInterfaces()
    {
        $interfaces = new ArrayCollection(['VirtualInterface', 'VirtualInterface2']);
        $this->assertEquals($interfaces, $this->object->getInterfaces());
    }

    public function testTraits()
    {
        $traits = new ArrayCollection(['FalseTrait', 'FalseTrait2']);
        $this->assertEquals($traits, $this->object->getTraits());
    }

    public function testProperties()
    {
        $rawProp = <<<'PROP'
    /**
     * @var string
     */
    protected $testProp;
PROP;
        $prop = new Property(new PropertyManipulator(String::create($rawProp)->trim()));
        $properties = new ArrayCollection([(string) $prop->getName() => $prop]);
        $this->assertEquals($properties, $this->object->getProperties());
    }

    public function testMethods()
    {
        $rawMethod = <<<'METHOD'
    /**
     * @param string $testProp
     * @param Interface2 $testProp2
     * @param array $testProp3
     */
    public function setTestProp($testProp, Interface2 $testProp2, array $testProp3 = [])
    {
        $this->testProp = $testProp . (string) $testProp2 . implode(', ', $testProp3);
    }
METHOD;

        $rawMethod2 = <<<'METHOD2'
    /**
     * @return string
     */
    public function getTestProp()
    {
        return $this->testProp;
    }
METHOD2;

        $method = new Method(new MethodManipulator(String::create($rawMethod)));
        $method2 = new Method(new MethodManipulator(String::create($rawMethod2)));
        $methods = new ArrayCollection();
        $methods->set((string) $method->getName()->trim(), $method);
        $methods->set((string) $method2->getName()->trim(), $method2);

        $this->assertEquals($methods->toArray(), $this->object->getMethods()->toArray());
    }

    protected function tearDown()
    {
        Mockery::close();
    }
}