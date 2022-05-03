<?php

namespace TBCD\FileArchiver\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use TBCD\FileArchiver\FileArchiver;
use TBCD\FileArchiver\FileArchiverInterface;

/**
 * @author Thomas Beauchataud
 * @since 02/05/2021
 */
class FileArchiverCompilerPass implements CompilerPassInterface
{

    /**
     * @inheritDoc
     */
    public function process(ContainerBuilder $container): void
    {
        $fileArchiverDefinition = new Definition(FileArchiver::class);
        if ($container->hasParameter('kernel.project_dir')) {
            $fileArchiverDefinition->setArgument(0, $container->getParameter('kernel.project_dir') . '/var/archive');
        }
        $container->setDefinition(FileArchiverInterface::class, $fileArchiverDefinition);
    }
}