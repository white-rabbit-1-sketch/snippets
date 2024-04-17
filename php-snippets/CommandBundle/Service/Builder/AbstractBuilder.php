<?php

namespace Shared\CommandBundle\Service\Builder;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\DbBundle\Annotation\Driver\Entity as EntityDriver;

/**
 * Class AbstractBuilder
 * @package Shared\CommandBundle\Service\Builder
 */
abstract class AbstractBuilder implements BuilderInterface
{
    /** @var EntityDriver */
    protected $entityDriver;

    /**
     * @param EntityDriver $entityDriver
     *
     * @DI\InjectParams({
     *     "entityDriver" = @DI\Inject("shared.db.annotation.driver.entity")
     * })
     */
    public function setEntityDriver(EntityDriver $entityDriver)
    {
        $this->entityDriver = $entityDriver;
    }
}
