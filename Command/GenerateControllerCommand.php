<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Tdn\PilotBundle\Manipulator\ControllerManipulator;
use Tdn\PilotBundle\OutputEngine\OutputEngineInterface;

/**
 * Class GenerateControllerCommand
 * @package Tdn\PilotBundle\Command
 */
class GenerateControllerCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'tdn:generate:controller';

    /**
     * @var string
     */
    const DESCRIPTION = 'Generates a Restful controller based on a doctrine entity.';

    /**
     * @return InputOption[]
     */
    protected function getInputArgs()
    {
        return [
            new InputOption(
                'resource',
                'r',
                InputOption::VALUE_NONE,
                'The object will return with the resource name'
            ),
            new InputOption(
                'document-api',
                'a',
                InputOption::VALUE_NONE,
                'Use NelmioApiDocBundle to document the controller'
            )
        ];
    }

    /**
     * @param  string $routePrefix
     *
     * @return string
     */
    public function getRoutePrefix($routePrefix = '')
    {
        $prefix = !empty($routePrefix) ? $routePrefix:
            strtolower(str_replace(array('\\', '/'), '_', $this->getEntity()));

        if ($prefix && '/' === $prefix[0]) {
            $prefix = substr($prefix, 1);
        }

        return $prefix;
    }

    /**
     * @param InputInterface          $input
     * @param OutputEngineInterface   $outputEngine
     * @param BundleInterface         $bundle
     * @param ClassMetadataInfo       $metadata
     *
     * @return ControllerManipulator
     */
    protected function createManipulator(
        InputInterface $input,
        OutputEngineInterface $outputEngine,
        BundleInterface $bundle,
        ClassMetadataInfo $metadata
    ) {
        $manipulator = new ControllerManipulator($outputEngine, $bundle, $metadata);
        $manipulator->setResource(($input->getOption('resource') ? true : false));
        $manipulator->setDocument(($input->getOption('document-api') ? true : false));
        $manipulator->setRoutePrefix($this->getRoutePrefix($input->getOption('route-prefix')));
        $manipulator->setGenerateTests(false);

        return $manipulator;
    }

    /**
     * @return string
     */
    protected function getFileType()
    {
        return 'Rest Controller';
    }
}
