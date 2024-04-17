<?php

namespace Shared\DbBundle\Annotation\Driver;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityRepository;

use JMS\DiExtraBundle\Annotation as DI;

use Shared\DbBundle\Entity\DoctrineTrait;
use Shared\UtilBundle\Annotation\Driver\PropertyName;
use Shared\UtilBundle\Entity\PropertyAccessorTrait;
use Shared\DbBundle\Annotation\Entity as AnnotationEntity;
use Shared\DbBundle\Exception\Annotation\Driver\EntityNotFoundException;

/**
 * Class Entity
 * @package Shared\UtilBundle\Annotation\Driver
 *
 * @DI\Service("shared.db.annotation.driver.entity", public=true)
 */
class Entity
{
    use DoctrineTrait;
    use PropertyAccessorTrait;

    /** @var int */
    protected $expectedEntityCount;

    /**
     * @param $object
     */
    public function fill($object)
    {
        $reader = new AnnotationReader();
        $reflectionObject = new \ReflectionObject($object);

        /** @var \ReflectionProperty $reflectionProperty */
        foreach ($reflectionObject->getProperties() as $reflectionProperty) {
            $annotationData = $reader->getPropertyAnnotation($reflectionProperty, AnnotationEntity::class);
            if ($annotationData) {
                $this->handleAnnotation($object, $annotationData, $reflectionProperty);
            }
        }
    }

    /**
     * @param $object
     * @param AnnotationEntity $annotation
     * @param \ReflectionProperty $reflectionProperty
     */
    protected function handleAnnotation(
        $object,
        AnnotationEntity $annotation,
        \ReflectionProperty $reflectionProperty
    ): void {
        $this->expectedEntityCount = 1;
        $repository = $this->doctrine->getRepository($annotation->value, $annotation->persistentManager);
        $criteria = $this->buildSearchCriteria(
            $repository,
            $annotation->persistentManager,
            $object,
            $annotation->mapping
        );

        if ($criteria) {
            $collection = $repository->matching($criteria);
            if ($annotation->exists) {
                $this->checkExists($reflectionProperty, $collection);
            }
            $propertyValue = $annotation->list ? $collection : $collection->first();
            if ($propertyValue) {
                $this->propertyAccessor->setValue($object, $reflectionProperty->getName(), $propertyValue);
            }
        }
    }

    /**
     * @param \ReflectionProperty $reflectionProperty
     * @param Collection $collection
     * @throws EntityNotFoundException
     */
    protected function checkExists(\ReflectionProperty $reflectionProperty, Collection $collection): void
    {
        if ($this->expectedEntityCount != $collection->count()) {
            $e = new EntityNotFoundException();
            $e->setPropertyName($reflectionProperty->getName());
            throw $e;
        }
    }

    /**
     * @param array $mapping
     * @param array $primaryKeyList
     * @return array
     */
    protected function prepareMapping(array $mapping, array $primaryKeyList = []): array
    {
        if (!$mapping) {
            foreach ($primaryKeyList as $primaryKey) {
                $mapping[$primaryKey] = $primaryKey;
            }
        }

        return $mapping;
    }

    /**
     * @param EntityRepository $repository
     * @param null|string $persistentManager
     * @param $object
     * @param array $mapping
     * @return bool|Criteria
     * @throws \Exception
     */
    protected function buildSearchCriteria(
        EntityRepository $repository,
        ?string $persistentManager,
        $object,
        array $mapping = []
    ) {
        $mapping = $this->prepareMapping(
            $mapping,
            $this->getPrimaryKeyFieldList($repository->getClassName(), $persistentManager)
        );

        $criteria = Criteria::create();

        foreach ($mapping as $keyField => $objectPropertyName) {
            $objectPropertyName = PropertyName::getPropertyByName(get_class($object), $objectPropertyName);
            $objectPropertyValue = $this->propertyAccessor->getValue($object, $objectPropertyName);

            if (is_null($objectPropertyValue)) {
                return false;
            }

            if (is_array($objectPropertyValue)) {
                $this->expectedEntityCount = count($objectPropertyValue);
            }

            /** @TODO: now we can't detect array values in primary keys left part. whatever. */

            $criteria->andWhere(Criteria::expr()->in($keyField, $this->prepareValue($objectPropertyValue)));
        }

        return $criteria;
    }

    /**
     * @param $value
     * @return array
     */
    protected function prepareValue($value): array
    {
        if (is_array($value)) {
            $value = (array) array_map(function ($value) {
                return (string) $value;
            }, (array) $value);
        } elseif (!is_object($value)) {
            $value = [(string) $value];
        } else {
            $value = [$value];
        }

        return $value;
    }

    /**
     * @param array $mapping
     * @param string $key
     * @return string
     * @throws \Exception
     */
    protected function getMappingValueByKey(array $mapping, string $key): string
    {
        if (!array_key_exists($key, $mapping)) {
            throw new \Exception(sprintf('Key "%s" not found in mapping', $key));
        }

        return $mapping[$key];
    }

    /**
     * @param string $entityClass
     * @param null|string $persistentManager
     * @return array
     * @throws \Exception
     */
    protected function getPrimaryKeyFieldList(string $entityClass, ?string $persistentManager): array
    {
        $primaryKeyFieldList = $this->doctrine->getManager($persistentManager)
            ->getClassMetadata($entityClass)->getIdentifierFieldNames();

        if (!$primaryKeyFieldList) {
            throw new \Exception(sprintf('Primary key of entity "%s" not found', $entityClass));
        }

        return $primaryKeyFieldList;
    }
}