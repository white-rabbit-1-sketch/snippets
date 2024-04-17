<?php

/**
Аннотация для кастомной генерации составного ключа новой сущности.
Причина - некорректная (legacy) архитектура БД, однако на качество кода это не влияет.

Пример использования:
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @CompositeKeyIdGenerator
     *
    protected $gatewayId;

*/

namespace Shared\DbBundle\Annotation\Driver;

use Doctrine\Common\Annotations\AnnotationReader;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\DbBundle\Annotation\CompositeKeyIdGenerator as AnnotationCompositeKeyIdGenerator;
use Shared\DbBundle\Service\Strategy\Generator\CompositeKeyIdGeneratorService;
use Shared\UtilBundle\Entity\PropertyAccessorTrait;

/**
 * Class CompositeKeyIdGenerator
 * @package Shared\UtilBundle\Annotation\Driver
 *
 * @DI\Service("shared.db.annotation.driver.composite_key_id_generator", public=true)
 */
class CompositeKeyIdGenerator
{
    use PropertyAccessorTrait;

    /** @var CompositeKeyIdGeneratorService */
    protected $generator;

    /**
     * CompositeKeyIdGenerator constructor.
     * @param CompositeKeyIdGeneratorService $generator
     *
     * @DI\InjectParams({
     *     "generator" = @DI\Inject("shared.db.service.strategy.generator.composite_key_id_generator")
     * })
     */
    public function __construct(
        CompositeKeyIdGeneratorService $generator
    ) {
        $this->generator = $generator;
    }

    /**
     * @param $object
     */
    public function fillPersistenceKey($object): void
    {
        $reader = new AnnotationReader();
        $reflectionObject = new \ReflectionObject($object);

        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($reflectionObject->getProperties() as $reflectionProperty) {
            $annotationData = $reader->getPropertyAnnotation($reflectionProperty, AnnotationCompositeKeyIdGenerator::class);
            if ($annotationData) {
                $this->handleAnnotation($object, $annotationData, $reflectionProperty);
            }
        }
    }

    /**
     * @param $object
     * @param AnnotationCompositeKeyIdGenerator $annotation
     * @param \ReflectionProperty $reflectionProperty
     */
    protected function handleAnnotation(
        $object,
        AnnotationCompositeKeyIdGenerator $annotation,
        \ReflectionProperty $reflectionProperty
    ): void {
        $this->propertyAccessor->setValue(
            $object,
            $reflectionProperty->getName(),
            $this->generator->generate($object)
        );
    }
}