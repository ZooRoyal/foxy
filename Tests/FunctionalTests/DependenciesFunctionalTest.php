<?php

namespace Foxy\Tests\FunctionalTests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @internal
 */
final class DependenciesFunctionalTest extends TestCase
{
    private $filesystem;

    protected function setUp()
    {
        $this->filesystem = new Filesystem();

        $this->cleanUpFiles();
    }

    protected function tearDown()
    {
        $this->cleanUpFiles();
    }

    /**
     * Test foxy setting Dev & Prod dependencies.
     *
     * @large
     */
    public function testInstallationOfDependencies()
    {
        $returnValue = '';

        $copyCommand = 'cp -R '.__DIR__.'/../../ '.' /tmp/foxyroot';

        exec($copyCommand, $unused, $returnValue);

        static::assertEquals(0, $returnValue, 'Can\'t copy project to tmp dir.');

        exec('composer -q --working-dir='.__DIR__.'/foxyMain install', $unused, $returnValue);

        static::assertSame(
            0,
            $returnValue,
            'Composer could not install test project foxyMain.'
        );

        $packageJson = file_get_contents(__DIR__.'/foxyMain/package.json');
        $packageLockJson = file_get_contents(__DIR__.'/foxyMain/package-lock.json');

        exec('composer -q --working-dir='.__DIR__.'/foxyMain install --no-dev', $unused, $returnValue);

        static::assertSame(
            0,
            $returnValue,
            'Composer could not install test project foxyMain with --no-dev.'
        );

        $packageJsonNoDev = file_get_contents(__DIR__.'/foxyMain/package.json');
        $packageLockJsonNoDev = file_get_contents(__DIR__.'/foxyMain/package-lock.json');

        static::assertSame($packageJson, $packageJsonNoDev);
        static::assertSame($packageLockJson, $packageLockJsonNoDev);
    }

    /**
     * Deleting files used while testing.
     */
    private function cleanUpFiles()
    {
        $this->filesystem->remove(__DIR__.'/foxyMain/composer.lock');
        $this->filesystem->remove(__DIR__.'/foxyMain/node_modules');
        $this->filesystem->remove(__DIR__.'/foxyMain/package.json');
        $this->filesystem->remove(__DIR__.'/foxyMain/yarn.lock');
        $this->filesystem->remove(__DIR__.'/foxyMain/package-lock.json');
        $this->filesystem->remove(__DIR__.'/foxyMain/vendor');
        $this->filesystem->remove('/tmp/foxyroot/');
    }
}
