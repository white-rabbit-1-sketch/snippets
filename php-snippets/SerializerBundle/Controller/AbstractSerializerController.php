<?php

namespace Shared\SerializerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Helper\SerializerTrait;
use Shared\UtilBundle\Entity\ContainerTrait;
use Shared\CacheBundle\Helper\AppCacheTrait;

/**
 * Class AbstractSerializerController
 * @package Shared\SerializerBundle\Controller
 *
 * @property ContainerInterface $container
 *
 * @DI\Service
 */
abstract class AbstractSerializerController extends Controller
{
    use SerializerTrait;
    use AppCacheTrait;
    use ContainerTrait;

    /** @var RequestStack */
    protected $requestStack;

    /**
     * @param RequestStack $requestStack
     *
     * @DI\InjectParams({
     *     "requestStack" = @DI\Inject("request_stack")
     * })
     */
    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    /**
     * @param $data
     * @param null $cacheTime
     * @param int $httpStatus
     * @return Response
     */
    protected function buildResponse($data, $cacheTime = null, $httpStatus = Response::HTTP_OK): Response
    {
        $serializedData = $this->serializerService->prepare($data, $cacheTime);

        return new JsonResponse($serializedData, $httpStatus);
    }
}
