<?php

/**
Абстрактный класс для работы с redis хранилищами.
*/

namespace Shared\UtilBundle\Repository\Redis;

use \Redis;

use Shared\UtilBundle\Entity\SerializerTrait;
use Shared\UtilBundle\Serializer\Type;

/**
 * Class AbstractRepository
 * @package Shared\UtilBundle\Repository
 */
abstract class AbstractRepository
{
    use SerializerTrait;

    /**
     * @param $object
     * @return string
     */
    abstract protected function getStoreKey($object): string;

    /**
     * @return string
     */
    abstract protected function getStoreKeyMask(): string;

    /**
     * @return string
     */
    abstract protected function getEntityClass(): string;

    /**
     * @return Redis
     */
    abstract protected function getRedisClient(): Redis;

    /**
     * @return int
     */
    abstract protected function getExpireTime(): int;

    /**
     * @param $object
     */
    public function persist($object): void
    {
        $this->getRedisClient()->set(
            $this->getStoreKey($object),
            $this->serializer->serialize($object, Type::JSON),
            $this->getExpireTime()
        );
    }

    /**
     * @param $object
     */
    public function remove($object): void
    {
        $this->getRedisClient()->del($this->getStoreKey($object));
    }

    /**
     * @param $object
     * @return bool
     */
    public function isExists($object): bool
    {
        return (bool) $this->getRedisClient()->exists($this->getStoreKey($object));
    }

    /**
     * @return array
     */
    public function getList(): array
    {
        $result = [];

        $keyList = $this->getRedisClient()->keys($this->getStoreKeyMask());
        if ($keyList) {
            $serializedList = (array) $this->getRedisClient()->getMultiple($keyList);

            foreach ($serializedList as $serializedObject) {
                if (!$serializedObject) {
                    continue;
                }

                $result[] = $this->serializer->deserialize(
                    $serializedObject,
                    $this->getEntityClass(),
                    Type::JSON
                );
            }
        }

        return $result;
    }
}