<?php

namespace Tdn\PilotBundle\Services\Utils\Doctrine;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\Finder\Finder;
use Sensio\Bundle\GeneratorBundle\Command\Validators;
use Tdn\PhpTypes\Type\String;

/**
 * Class EntityUtils
 * @package Tdn\PilotBundle\Services\Utils\Doctrine
 */
class EntityUtils
{
    /**
     * @param string|null $directory
     * @param array|string $exclude
     *
     * @return ArrayCollection
     */
    public function getEntityDirAsCollection($directory = null, array $exclude = [])
    {
        $entities = new ArrayCollection();

        if (null !== $directory) {
            $finder = new Finder();
            $finder
                ->files()
                ->in($directory)
                ->name('*.php')
                ->notName('/interface/i')
                ->notName('/manager/i')
                ->notName('/repository/i')
            ;

            foreach ($exclude as $toExclude) {
                $finder->notName(
                    sprintf(
                        '/%s/i',
                        $toExclude
                    )
                );
            }

            /** @var \SplFileInfo $file */
            foreach ($finder as $file) {
                $entities->add($this->getEntityShortcutFromPath($file->getRealPath()));
            }
        }

        return $entities;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function getEntityShortcutFromPath($path)
    {
        try {
            //Return if already in shortcut format
            return Validators::validateEntityName($path);
        } catch (\InvalidArgumentException $e) {
            $path = String::create($path);

            if (!$path->toLowerCase()->contains('entity')) {
                throw new \InvalidArgumentException('Entity directory must be "Entity" at this time.');
            }

            $bundleDir = realpath((string) $path->subStrUntil('/Entity', true));

            $bundleFinder = new Finder();
            $bundleFinder
                ->files()
                ->in($bundleDir)
                ->name('*Bundle.php')
            ;

            if ($bundleFinder->count() !== 1) {
                throw new \RuntimeException('Please make sure there is only *one* bundle class.');
            }

            $bundleName = null;
            /** @var \SplFileInfo $file */
            foreach ($bundleFinder as $file) {
                $bundleName = (string) String::create($file->openFile()->getFilename())->removeRight('.php');
            }

            $entityName = $path->subStrAfter('/Entity/', true)->removeRight('.php');

            return $bundleName . ':' . $entityName;
        }
    }

    /**
     * @param ManagerRegistry $doctrine
     * @param string          $entity
     *
     * @return ClassMetadata
     */
    public function getMetadata(ManagerRegistry $doctrine, $entity)
    {
        try {
            return $doctrine->getManagerForClass($entity)
                ->getMetadataFactory()
                ->getMetadataFor($entity);
        } catch (\Exception $e) {
            throw new \RuntimeException(
                sprintf(
                    'Could not find metadata for class %s. Error: %s',
                    $entity,
                    $e->getMessage()
                )
            );
        }
    }

    /**
     * Returns an array containing the bundle and the entity.
     *
     * @param string $shortcut
     *
     * @return string[]
     */
    public function parseShortcutNotation($shortcut)
    {
        $entity = str_replace('/', '\\', $shortcut);

        if (false === $pos = strpos($entity, ':')) {
            throw new \InvalidArgumentException(
                sprintf(
                    'The entity name must contain a : ("%s" given, expecting something like AcmeBlogBundle:Blog/Post)',
                    $entity
                )
            );
        }

        return array(substr($entity, 0, $pos), substr($entity, $pos + 1));
    }
}
