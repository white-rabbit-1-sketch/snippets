<?php

namespace Shared\SerializerBundle\Helper;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Service\NormalizerService;

/**
 * Class NormalizerTrait
 * @package Shared\SerializerBundle\Helper
 *
 * @DI\Service
 */
trait NormalizerTrait
{
    /** @var NormalizerService */
    protected $normalizerService;

    /**
     * @param NormalizerService $normalizerService
     *
     * @DI\InjectParams({
     *     "normalizerService" = @DI\Inject("shared.serializer.service.normalizer")
     * })
     */
    public function setNormalizerService(NormalizerService $normalizerService): void
    {
        $this->normalizerService = $normalizerService;
    }
}