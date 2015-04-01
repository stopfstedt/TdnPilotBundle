<?php

namespace Tdn\PilotBundle\Manipulator;

use Tdn\PhpTypes\Type\String;
use Tdn\PilotBundle\Model\File;
use Tdn\PilotBundle\Model\FileInterface;
use Tdn\PilotBundle\Model\Format;

/**
 * Class ManagerManipulator
 * @package Tdn\PilotBundle\Manipulator
 */
class ManagerManipulator extends AbstractServiceManipulator
{
    /**
     * Sets up an Entity Manager based on entity.
     * Sets up an Entity Manager interface.
     * @return $this
     */
    public function prepare()
    {
        $entityReflection = $this->getMetadata()->getReflectionClass();

        $entityConstructor = ($entityReflection) ?
            ($entityReflection->hasMethod('__construct')) ? $entityReflection->getMethod('__construct') : null : null;

        $path = sprintf(
            '%s' . DIRECTORY_SEPARATOR . 'Entity' . DIRECTORY_SEPARATOR . 'Manager',
            ($this->getTargetDirectory()) ?: $this->getBundle()->getPath()
        );

        $this->addManagerFile($path, $entityConstructor);
        $this->addManagerInterfaceFile($path, $entityConstructor);

        if ($this->getFormat() !== Format::ANNOTATION) {
            $this->addManagerServiceFile();
            $this->setUpdatingDiConfFile(true);
        }

        return $this;
    }

    /**
     * @param string $path
     * @param \ReflectionMethod|null $entityConstructor
     */
    protected function addManagerFile($path, $entityConstructor = null)
    {
        $manager = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . '%sManager.php',
                $path,
                $this->getEntity()
            )
        );

        $manager->setContents($this->getManagerContent($entityConstructor));

        $this->addFile($manager);
    }

    /**
     * @param string $path
     * @param \ReflectionMethod|null $entityConstructor
     */
    protected function addManagerInterfaceFile($path, $entityConstructor = null)
    {
        $managerInterface = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . '%sManagerInterface.php',
                $path,
                $this->getEntity()
            )
        );

        $managerInterface
            ->setContents($this->getManagerInterfaceContent($entityConstructor))
            ->setAuxFile(true)
        ;

        $this->addFile($managerInterface);
    }

    /**
     * @return void
     */
    protected function addManagerServiceFile()
    {
        $serviceFile = new File(
            sprintf(
                '%s' . DIRECTORY_SEPARATOR . 'Resources' .
                DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'managers.%s',
                ($this->getTargetDirectory()) ?: $this->getBundle()->getPath(),
                $this->getFormat()
            )
        );

        $serviceFile
            ->setContents($this->getServiceFileContents($serviceFile))
            ->setServiceFile(true)
        ;

        $this->addMessage(sprintf(
            'Make sure to load "%s" in your extension file to enable the new services.',
            $serviceFile->getBasename()
        ));

        $this->addFile($serviceFile);
    }

    /**
     * Generates string containing params in following format:
     * Interface $param, array $param = [], $param...etc
     *
     * @param \ReflectionMethod|null $method
     * @return string
     */
    protected function getParams(\ReflectionMethod $method = null)
    {
        $outParams = '';
        if (null !== $method) {
            /** @var \ReflectionParameter $param */
            foreach ($method->getParameters() as $param) {
                $typeHint = $this->getTypeHintType($param);

                try {
                    $default = ' = ' . $param->getDefaultValue() . ', ';
                } catch (\ReflectionException $e) {
                    $default = ', ';
                }

                $outParams .= sprintf('%s$%s%s', $typeHint, $param->getName(), $default);
            }
        }

        return (string) String::create($outParams)->removeRight(', ');
    }

    /**
     * @param \ReflectionParameter|null $param
     * @return string
     */
    protected function getTypeHintType(\ReflectionParameter $param = null)
    {
        $hint = '';

        if ($param !== null) {
            if ($param->getClass() !== null) {
                return $param->getClass()->getName();
            }

            if ($param->isArray()) {
                return 'array';
            }

            if ($param->isCallable()) {
                return 'Callable';
            }
        }

        return $hint;
    }

    /**
     * Generates string containing params in following format:
     * $param1, $param2, $param3...etc
     *
     * @param \ReflectionMethod|null $method
     * @return string
     */
    protected function getConstructParams(\ReflectionMethod $method = null)
    {
        $params = '';

        if ($method) {
            /** @var \ReflectionParameter $param */
            foreach ($method->getParameters() as $param) {
                $params .= sprintf('$%s, ', $param->getName());
            }
        }

        return (string) String::create($params)->removeRight(', ');
    }

    /**
     * @param \ReflectionMethod|null $constructorMethod
     * @return string
     */
    protected function getManagerContent(\ReflectionMethod $constructorMethod = null)
    {
        return $this->getTemplateStrategy()->render(
            'manager/manager.php.twig',
            [
                'entity'                  => $this->getEntity(),
                'entity_namespace'        => $this->getEntityNamespace(),
                'namespace'               => $this->getBundle()->getNamespace(),
                'entity_construct_params' => $this->getParams($constructorMethod),
                'construct_params'        => $this->getConstructParams($constructorMethod)
            ]
        );
    }

    /**
     * @param \ReflectionMethod|null $constructorMethod
     * @return string
     */
    protected function getManagerInterfaceContent(\ReflectionMethod $constructorMethod = null)
    {
        return $this->getTemplateStrategy()->render(
            'manager/interface.php.twig',
            [
                'entity'                  => $this->getEntity(),
                'entity_namespace'        => $this->getEntityNamespace(),
                'namespace'               => $this->getBundle()->getNamespace(),
                'entity_construct_params' => $this->getParams($constructorMethod)
            ]
        );
    }

    /**
     * @param File $file
     *
     * @return string
     */
    protected function getServiceFileContents(File $file)
    {
        $serviceClass = sprintf(
            '%s\\Entity\\Manager\\%sManager',
            $this->getBundle()->getNamespace(),
            $this->getEntity()
        );

        $paramKey = sprintf(
            '%s.%s.manager.class',
            (string) String::create($this->getBundle()->getName())->toLowerCase()->replace('bundle', ''),
            strtolower($this->getEntity())
        );

        $serviceId = sprintf(
            '%s.%s.manager',
            (string) String::create($this->getBundle()->getName())->toLowerCase()->replace('bundle', ''),
            strtolower($this->getEntity())
        );

        $service = [
            'class' => '%' . $paramKey . '%',
            'arguments' => [
                '@doctrine',
                sprintf(
                    '%s\\Entity\\%s%s',
                    $this->getBundle()->getNamespace(),
                    $this->getEntityNamespace(),
                    $this->getEntity()
                )
            ]
        ];

        $serviceUtils = $this->getServiceUtils();

        return $serviceUtils
            ->setFile($file)
            ->addParameter($paramKey, $serviceClass)
            ->addService($serviceId, $service)
            ->getFormattedContents($this->getFormat())
        ;
    }
}
