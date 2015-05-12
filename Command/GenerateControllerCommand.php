<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tdn\PilotBundle\Manipulator\ControllerManipulator;
use \SplFileInfo;

/**
 * Class GenerateControllerCommand
 *
 * Generates a CRUD controller based on an entity.
 *
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
     * @var bool
     */
    protected $resource;

    /**
     * @var bool
     */
    protected $swagger;

    /**
     * @var string
     */
    protected $routePrefix;

    /**
     * @var bool
     */
    protected $generateTests;

    /**
     * @var string
     */
    protected $fixturesPath;

    /**
     * @var string
     */
    protected $dataPath;

    /**
     * @var bool
     */
    protected $forcedTests;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->addOption(
                'resource',
                '',
                InputOption::VALUE_NONE,
                'The object will return with the resource name'
            )
            ->addOption(
                'with-swagger',
                '',
                InputOption::VALUE_NONE,
                'Use NelmioApiDocBundle (which uses swagger-ui) to document the controller'
            )
            ->addOption(
                'route-prefix',
                '',
                InputOption::VALUE_NONE,
                'If using annotations, you should also add a route prefix to the controller.'
            )
            ->addOption(
                'generate-tests',
                '',
                InputOption::VALUE_NONE,
                'Use flag to generate standard CRUD tests. ' .
                'Requires doctrine fixtures to be present. Specifications in Readme.'
            )
            ->addOption(
                'fixtures-path',
                '',
                InputOption::VALUE_NONE,
                'Absolute path to DoctrineFixture files. Required if generating tests.'
            )
            ->addOption(
                'static-data-path',
                '',
                InputOption::VALUE_NONE,
                'Absolute path to static data files that implement the DataInterface provided in this bundle. ' .
                'This might be automatically generated at a later time.'
            )
            ->addOption(
                'force-tests',
                '',
                InputOption::VALUE_NONE,
                'Generate Controller tests even for controllers with missing fixture files.'
            )
        ;

        parent::configure();
    }

    /**
     * Gets the route prefix for the resource
     *
     * Gets a route prefix to use when using annotations. Otherwise the route prefix
     * is set through the `RoutingManipulator`.
     *
     * @param  string $routePrefix
     *
     * @return string
     */
    public function getRoutePrefix($routePrefix = '')
    {
        $prefix = (!empty($routePrefix)) ? $routePrefix:
            strtolower(str_replace(array('\\', '/'), '_', $this->getEntity()));

        if ($prefix && '/' === $prefix[0]) {
            $prefix = substr($prefix, 1);
        }

        return $prefix;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->resource = ($input->getOption('resource') ? true : false);
        $this->swagger  = ($input->getOption('with-swagger') ? true : false);
        $this->routePrefix = $this->getRoutePrefix($input->getOption('route-prefix'));
        $this->generateTests = ($input->getOption('generate-tests') ? true : false);
        $this->fixturesPath = ($input->getOption('fixtures-path')) ?
            new SplFileInfo($input->getOption('fixtures-path')) : new SplFileInfo(null);
        $this->forcedTests = ($input->getOption('force-tests') ? true : false);
        $this->dataPath    = ($input->getOption('static-data-path')) ?
            new SplFileInfo($input->getOption('static-data-path')) : new SplFileInfo(null);

        //For now dataPath is required (until we can auto generate those.
        //Same goes for fixtures.
        if ($this->generateTests && (!$this->fixturesPath->isDir() || !$this->dataPath->isDir())) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Directory %s is not valid.',
                    (!$this->fixturesPath->isDir()) ?
                        $input->getOption('fixtures-path') : $input->getOption('static-data-path')
                )
            );
        }

        return parent::execute($input, $output);
    }

    /**
     * @return ControllerManipulator
     */
    protected function createManipulator()
    {
        $manipulator = new ControllerManipulator();
        $manipulator->setResource($this->resource);
        $manipulator->setSwagger($this->swagger);
        $manipulator->setRoutePrefix($this->routePrefix);
        $manipulator->setGenerateTests($this->generateTests);
        $manipulator->setFixturesPath($this->fixturesPath);
        $manipulator->setForcedTests($this->forcedTests);
        $manipulator->setDataPath($this->dataPath);

        return $manipulator;
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Rest Controller'];
    }
}
