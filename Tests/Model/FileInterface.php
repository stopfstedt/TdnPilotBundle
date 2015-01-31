<?php

namespace Tdn\SfProjectGeneratorBundle\Tests\Model;

/**
 * Interface FileInterface
 * @package Tdn\SfProjectGeneratorBundle\Tests\Model
 */
interface FileInterface
{
    const FILE_CONTENTS = <<<'EOT'
<?php

namespace Tdn\SfProjectGeneratorBundle\Test\Model\Virtual;

use \StdClass;
use \SplFileInfo;

/**
 * @author Victor Passapera
 */
class VirtualClass extends StdClass implements VirtualInterface, VirtualInterface2
{
    use FalseTrait;
    use FalseTrait2;

    /**
     * @var string
     */
    protected $testProp;

    /**
     * @param string $testProp
     * @param Interface2 $testProp2
     * @param array $testProp3
     */
    public function setTestProp($testProp, Interface2 $testProp2, array $testProp3 = [])
    {
        $this->testProp = $testProp . (string) $testProp2 . implode(', ', $testProp3);
    }

    /**
     * @return string
     */
    public function getTestProp()
    {
        return $this->testProp;
    }
}

EOT;
}