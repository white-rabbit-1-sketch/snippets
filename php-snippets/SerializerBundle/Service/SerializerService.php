<?php

namespace Shared\SerializerBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;

use Symfony\Component\Cache\CacheItem;

use Shared\CacheBundle\Helper\SerializerCacheTrait;
use Shared\SerializerBundle\Helper\BuilderTrait;

/**
 * Class SerializerService
 * @package Shared\SerializerBundle\Service
 *
 * @DI\Service("shared.serializer.service.serializer", public=true)
 */
class SerializerService
{
    use BuilderTrait;
    use SerializerCacheTrait;

    /**
     * @param $data
     * @param int|null|null $cacheTime
     * @return mixed
     */
    public function prepare($data, ?int $cacheTime = null)
    {
        $cacheItem = new CacheItem();

        if ($cacheTime) {
            $cacheItem = $this->serializerCacheService->getItem(sprintf(
                'serializer-data-%s',
                md5(serialize($data))
            ));
        }

        if (!$cacheItem->isHit()) {
            $cacheItem->set($this->builderService->build($data));
            $cacheItem->expiresAfter($cacheTime);

            if ($cacheTime) {
                $this->serializerCacheService->save($cacheItem);
            }
        }

        return $cacheItem->get();
    }

    /**
     * @param $data
     * @return string
     */
    public function serialize($data)
    {
        return json_encode($this->prepare($data));
    }
}
