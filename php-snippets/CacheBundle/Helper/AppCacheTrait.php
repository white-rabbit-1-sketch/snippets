<?php

namespace Shared\CacheBundle\Helper;

use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapter;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * Class AppCacheTrait
 * @package Shared\CacheBundle\Helper
 *
 * @DI\Service
 */
trait AppCacheTrait
{
    /** @var TagAwareAdapter */
    protected $appCacheService;

    /**
     * @param AdapterInterface $appCacheService
     *
     * @DI\InjectParams({
     *     "appCacheService" = @DI\Inject("cache.app")
     * })
     */
    public function setAppCacheService(AdapterInterface $appCacheService): void
    {
        $this->appCacheService = new TagAwareAdapter($appCacheService, $appCacheService);
    }
}