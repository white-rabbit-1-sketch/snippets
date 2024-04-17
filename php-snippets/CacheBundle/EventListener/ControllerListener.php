<?php

namespace Shared\CacheBundle\EventListener;

use Symfony\Component\Cache\CacheItem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Doctrine\Common\Annotations\AnnotationReader;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\CacheBundle\Annotation\Cache as AnnotationCache;
use Shared\CacheBundle\Annotation\InvalidateCache as AnnotationInvalidateCache;
use Shared\UtilBundle\Entity\LoggerTrait;
use Shared\CacheBundle\Helper\AppCacheTrait;

/**
 * Class ControllerListener
 * @package Shared\CacheBundle\EventListener
 *
 * @DI\Service("shared.cache.event_listener.request", public=true)
 */
class ControllerListener
{
    use AppCacheTrait;
    use LoggerTrait;

    /** @var AnnotationCache */
    protected $cacheAnnotationData;

    /** @var AnnotationInvalidateCache */
    protected $invalidateCacheAnnotationData;

    /** @var ParamConverter */
    protected $paramConverterAnnotationData;

    /** @var CacheItem */
    protected $cacheItem;

    protected $requestDto;

    /** @var bool */
    protected $isCacheEnabled;

    /**
     * @param bool $isCacheEnabled
     *
     * @DI\InjectParams({
     *     "isCacheEnabled" = @DI\Inject("%cache.controller.enabled%")
     * })
     */
    public function __construct(bool $isCacheEnabled)
    {
        $this->isCacheEnabled = $isCacheEnabled;
    }

    /**
     * @param FilterControllerArgumentsEvent $event
     *
     * @DI\Observe("kernel.controller_arguments", priority = 255)
     */
    public function onControllerArguments(FilterControllerArgumentsEvent $event)
    {
        if ($this->isCacheEnabled) {
            $controllerInfo = $event->getController();

            is_array($controllerInfo) && $this->initAnnotationsData($controllerInfo[0], $controllerInfo[1]);

            if ($this->paramConverterAnnotationData) {
                $this->requestDto = $event->getRequest()->attributes->get($this->paramConverterAnnotationData->getName());
            }

            if ($this->cacheAnnotationData) {
                $this->cacheItem = $this->appCacheService->getItem($this->buildCacheKey(
                    $controllerInfo[0],
                    $controllerInfo[1],
                    $event->getArguments()
                ));

                /** @var Response $cachedResponse */
                $cachedResponse = $this->cacheItem->get();

                if ($cachedResponse) {
                    $this->logger->debug('Cache item hit', [
                        'key' => $this->cacheItem->getKey(),
                        'is_hit' => $this->cacheItem->isHit(),
                        'tags' => $this->cacheItem->getPreviousTags()
                    ]);

                    $event->setController(function () use ($cachedResponse) {
                        return $cachedResponse;
                    });
                }
            }
        }
    }

    /**
     * @param FilterResponseEvent $event
     *
     * @DI\Observe("kernel.response", priority = 255)
     */
    public function onResponse(FilterResponseEvent $event)
    {
        if ($this->isCacheEnabled) {
            if ($this->cacheAnnotationData && !$this->cacheItem->isHit()) {
                $this->cacheItem->set($event->getResponse());
                $this->cacheItem->expiresAfter($this->cacheAnnotationData->ttl);
                $tagList = $this->buildTagList($this->cacheAnnotationData->tags);
                $this->cacheItem->tag($tagList);
                $this->appCacheService->save($this->cacheItem);

                $this->logger->debug('Create cache item', [
                    'key' => $this->cacheItem->getKey(),
                    'tags' => $tagList
                ]);
            }

            if ($this->invalidateCacheAnnotationData) {
                $tagList = $this->buildTagList($this->invalidateCacheAnnotationData->tags);

                $this->logger->debug('Invalidate cache item by tags', [
                    'tags' => $tagList
                ]);

                $this->appCacheService->invalidateTags($tagList);
            }
        }
    }

    protected function buildTagList(array $tagList): array
    {
        $result = [];

        $language = new ExpressionLanguage();
        $evaluatorParameterList = [];
        if ($this->paramConverterAnnotationData) {
            $evaluatorParameterList[$this->paramConverterAnnotationData->getName()] = $this->requestDto;
        }

        foreach ($tagList as $tagExpression) {
            $result[] = $language->evaluate($tagExpression, $evaluatorParameterList);
        }

        return $result;
    }

    protected function initAnnotationsData($entity, string $method): void
    {
        $reader = new AnnotationReader();
        $reflectionObject = new \ReflectionClass($entity);
        $reflectionMethod = $reflectionObject->getMethod($method);
        $this->cacheAnnotationData = $reader->getMethodAnnotation(
            $reflectionMethod,
            AnnotationCache::class
        );
        $this->invalidateCacheAnnotationData = $reader->getMethodAnnotation(
            $reflectionMethod,
            AnnotationInvalidateCache::class
        );
        $this->paramConverterAnnotationData = $reader->getMethodAnnotation(
            $reflectionMethod,
            ParamConverter::class
        );
    }

    protected function buildCacheKey($entity, string $method, $argumentList): string
    {
        return sprintf(
            '%s-%s-%s',
            str_replace('\\', '_', get_class($entity)),
            $method,
            md5(serialize($argumentList))
        );
    }
}