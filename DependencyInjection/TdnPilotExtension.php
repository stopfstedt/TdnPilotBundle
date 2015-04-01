<?php

namespace Tdn\PilotBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class TdnPilotExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        //tdn_pilot.template.strategy.twig_template_strategy
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('template-strategies.yml');
        $loader->load('entity-utils.yml');

        $this->verifyDependencies($container);
        $this->setDefaultTemplateStrategy($config, $container);
    }

    protected function setDefaultTemplateStrategy(array $config, ContainerBuilder $container)
    {
        if (isset($config['template'])) {
            $container->setAlias(
                'tdn_pilot.template.strategy.default',
                'tdn_pilot.template.strategy.' . $config['template']['strategy']
            );
        }
    }

    protected function verifyDependencies(ContainerBuilder $container)
    {
        if (!$container->has('tuck_converter.config_format_converter')) {
            throw new \RuntimeException(
                'Please make sure that rosstuck/TuckConverterBundle is installed and configured'
            );
        }
    }

    public function getAlias()
    {
        return 'tdn_pilot';
    }
}
