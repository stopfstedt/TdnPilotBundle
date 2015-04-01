<?php

namespace Tdn\PilotBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Tdn\PilotBundle\Manipulator\FormManipulator;

/**
 * Class GenerateFormCommand
 *
 * Generates a form type class for a given Doctrine entity, with optional REST generator support.
 *
 * @package Tdn\PilotBundle\Command
 */
class GenerateFormCommand extends AbstractGeneratorCommand
{
    /**
     * @var string
     */
    const NAME = 'tdn:generate:form';

    /**
     * @var string
     */
    const DESCRIPTION = 'Generates a form type class based on a doctrine entity.';

    /**
     * @param InputInterface $input
     *
     * @return FormManipulator
     */
    protected function createManipulator(InputInterface $input)
    {
        return new FormManipulator();
    }

    /**
     * @return string[]
     */
    protected function getFiles()
    {
        return ['Form type'];
    }
}
