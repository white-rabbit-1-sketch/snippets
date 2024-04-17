<?php

namespace Shared\CommandBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\CommandBundle\Command\AbstractCommand;
use Shared\CommandBundle\Service\Builder\BuilderInterface;
use Shared\UtilBundle\Entity\ContainerTrait;

/**
 * Class BuilderService
 * @package Shared\CommandBundle\Service
 *
 * @DI\Service("shared.command.service.builder", public=true)
 */
class BuilderService
{
    use ContainerTrait;

    protected const BUILDER_PATTERN = 'shared.command.service.builder.%s';

    /**
     * @param string $name
     * @param array $data
     * @return AbstractCommand
     */
    public function buildCommand(string $name, array $data = []): AbstractCommand
    {
        return $this->getCommandBuilder($name)->buildCommand($name, $data);
    }

    /**
     * @param string $name
     * @return BuilderInterface
     */
    protected function getCommandBuilder(string $name): BuilderInterface
    {
        /** @var BuilderInterface $builder */
        $builder = $this->container->get(sprintf(self::BUILDER_PATTERN, $name));

        return $builder;
    }
}
