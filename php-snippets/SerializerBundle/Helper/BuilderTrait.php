<?php

namespace Shared\SerializerBundle\Helper;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Service\BuilderService;

/**
 * Class BuilderTrait
 * @package Shared\SerializerBundle\Helper
 *
 * @DI\Service
 */
trait BuilderTrait
{
    /** @var BuilderService */
    protected $builderService;

    /**
     * @param BuilderService $builderService
     *
     * @DI\InjectParams({
     *     "builderService" = @DI\Inject("shared.serializer.service.builder")
     * })
     */
    public function setBuilderService(BuilderService $builderService): void
    {
        $this->builderService = $builderService;
    }
}