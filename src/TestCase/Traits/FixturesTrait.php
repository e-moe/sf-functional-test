<?php

namespace PhpSolution\FunctionalTest\TestCase\Traits;

use PhpSolution\FunctionalTest\Fixtures\OdmFixtureLoader;
use PhpSolution\FunctionalTest\Fixtures\OrmFixtureLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * FixturesTrait
 */
trait FixturesTrait
{
    /**
     * @param array       $files
     * @param null|string $entityManagerName
     *
     * @return array
     */
    protected static function loadOrm(array $files, string $entityManagerName = null)
    {
        return (new OrmFixtureLoader(self::getContainer()))->load($files, $entityManagerName);
    }

    /**
     * @param array       $files
     * @param null|string $documentManagerName
     *
     * @return array
     */
    protected static function loadOdm(array $files, string $documentManagerName = null)
    {
        return (new OdmFixtureLoader(self::getContainer()))->load($files, $documentManagerName);
    }

    /**
     * @param array       $files
     * @param string|null $type expected values: null, orm, odm
     * @param string|null $objectManagerName
     *
     * @return mixed
     */
    protected static function load(array $files, string $type = null, string $objectManagerName = null)
    {
        switch (true)
        {
            case 'odm' === $type:
            case self::getContainer()->has('fidry_alice_data_fixtures.loader.doctrine_mongodb') && null === $type:
                return self::loadOdm($files, $objectManagerName);
            case 'orm' === $type:
            case self::getContainer()->has('fidry_alice_data_fixtures.loader.doctrine') && null === $type:
                return self::loadOrm($files, $objectManagerName);
            default:
                throw new \RuntimeException('Imposible situation, please check your doctrine configuration');
        }
    }

    /**
     * @param string $path
     * @param bool   $assoc
     *
     * @return array|\stdClass
     */
    protected static function getFixturesFromJson(string $path, bool $assoc = true)
    {
        return json_decode(file_get_contents(self::doLocateFile($path)), $assoc);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    private static function doLocateFile(string $path): string
    {
        $path = sprintf('%s/%s', 'tests/DataFixtures', $path);
        $realPath = realpath($path);

        if (false === $realPath || false === file_exists($realPath)) {
            throw new \LogicException(sprintf("File %s does not exist", $path));
        }

        return $realPath;
    }

    /**
     * @return ContainerInterface
     */
    abstract protected static function getContainer(): ContainerInterface;
}
