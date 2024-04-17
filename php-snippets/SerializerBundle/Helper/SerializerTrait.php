<?php

namespace Shared\SerializerBundle\Helper;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Service\SerializerService;

/**
 * Class SerializerTrait
 * @package Shared\SerializerBundle\Helper
 *
 * @DI\Service
 */
trait SerializerTrait
{
    /** @var SerializerService */
    protected $serializerService;

    /**
     * @param SerializerService $serializerService
     *
     * @DI\InjectParams({
     *     "serializerService" = @DI\Inject("shared.serializer.service.serializer")
     * })
     */
    public function setSerializerService(SerializerService $serializerService): void
    {
        $this->serializerService = $serializerService;
    }
}