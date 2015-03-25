<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Tdn\PilotBundle\Manipulator\ControllerManipulator;
use Tdn\PhpTypes\Type\String;
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
     * @var SplFileInfo
     */
    protected $fixturesPath;

    /**
     * @var SplFileInfo
     */
    protected $dataPath;

    /**
     * @var bool
     */
    protected $forcedTests;

    /**
     * @var int
     */
    protected $pathDepth;

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
                InputOption::VALUE_OPTIONAL,
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
                InputOption::VALUE_OPTIONAL,
                'Path to DoctrineFixture files. Required if generating tests.'
            )
            ->addOption(
                'data-path',
                '',
                InputOption::VALUE_OPTIONAL,
                'Path to static data files that implement the SeedInterface provided in this bundle. ' .
                'This might be automatically generated at a later time.'
            )
            ->addOption(
                'path-depth',
                '',
                InputOption::VALUE_OPTIONAL,
                'Depth to search to for the paths set as parameters (Affects both data and fixtures paths).',
                0
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
        $this->resource      = ($input->getOption('resource') ? true : false);
        $this->swagger       = ($input->getOption('with-swagger') ? true : false);
        $this->routePrefix   = $this->getRoutePrefix($input->getOption('route-prefix'));
        $this->generateTests = ($input->getOption('generate-tests') ? true : false);
        $this->fixturesPath  = ($input->getOption('fixtures-path')) ?
            new SplFileInfo($this->getAbsolutePath($input->getOption('fixtures-path'))) : new SplFileInfo(null);
        $this->forcedTests   = ($input->getOption('force-tests') ? true : false);
        $this->dataPath      = ($input->getOption('data-path')) ?
            new SplFileInfo($this->getAbsolutePath($input->getOption('data-path'))) : new SplFileInfo(null);
        $this->pathDepth     = $input->getOption('path-depth');

        //For now dataPath is required (until we can auto generate those.
        //Same goes for fixtures.
        if ($this->generateTests && (!$this->fixturesPath->isDir() || !$this->dataPath->isDir())) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Directory %s is not valid.',
                    (!$this->fixturesPath->isDir()) ?
                    $input->getOption('fixtures-path') : $input->getOption('data-path')
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
        $manipulator->setPathDepth($this->pathDepth);

        return $manipulator;
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Rest Controller'];
    }

    /**
     * Figures out absolute if relative path is passed in.
     *
     * @param string $relativePath
     * @return string
     */
    private function getAbsolutePath($relativePath)
    {
        $absolutePath = $relativePath = String::create($relativePath);

        if ($relativePath->startsWith('.') || !$relativePath->startsWith('/')) {
            $absolutePath = String::create(__FILE__)
                    ->replace('\\', '/')
                    ->subStrUntil('/vendor', true)
                    ->ensureRight(DIRECTORY_SEPARATOR . $this->getCleanPathName($relativePath))
            ;

        }

        return (string) $absolutePath;
    }

    /**
     * Returns a relative path name without the separator.
     *
     * @param \Tdn\PhpTypes\Type\String $path
     *
     * @return string
     */
    private function getCleanPathName(String $path)
    {
        if ($path[0] == '.' || $path[0] == '/') {
            if ($path[1] == '/') {
                return (string) $path->subStrAfter($path->at(2));
            }

            return (string) $path->subStrAfter($path->at(1));
        }

        return (string) $path;
    }
}
