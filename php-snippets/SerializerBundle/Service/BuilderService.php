<?php

namespace Shared\SerializerBundle\Service;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\SerializerBundle\Service\Builder\AbstractBuilder;
use Shared\UtilBundle\Entity\ContainerTrait;

/**
 * Class BuilderService
 * @package Shared\SerializerBundle\Service
 *
 * @DI\Service("shared.serializer.service.builder", public=true)
 */
class BuilderService
{
    use ContainerTrait;

    protected const BUILDER_TYPE_SERVICE_PATTERN = 'shared.serializer.service.builder.%s';

    /**
     * @param $data
     * @param int $level
     * @return mixed
     */
    public function build($data, int $level = 0)
    {
        $result = $data;

        if (!is_null($data) && !is_scalar($data)) {
            $builder = $this->getTypeBuilder(gettype($data));
            $result = $builder->buildType($data, ++$level);
        }

        return $result;
    }

    /**
     * @param string $type
     * @return AbstractBuilder
     */
    protected function getTypeBuilder(string $type): AbstractBuilder
    {
        /** @var AbstractBuilder $builder */
        $builder = $this->container->get(sprintf(self::BUILDER_TYPE_SERVICE_PATTERN, $type));

        return $builder;
    }
}
