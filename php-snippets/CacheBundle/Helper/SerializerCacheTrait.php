<?php

namespace Shared\CacheBundle\Helper;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class SerializerCacheTrait
 * @package Shared\CacheBundle\Helper
 *
 * @DI\Service
 */
trait SerializerCacheTrait
{
    /** @var TagAwareAdapter */
    protected $serializerCacheService;

    /**
     * @param AdapterInterface $serializerCacheService
     *
     * @DI\InjectParams({
     *     "serializerCacheService" = @DI\Inject("cache.serializer")
     * })
     */
    public function setSerializerCacheService(AdapterInterface $serializerCacheService): void
    {
        $this->serializerCacheService = new TagAwareAdapter($serializerCacheService, $serializerCacheService);
    }
}