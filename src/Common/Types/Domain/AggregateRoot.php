<?php
declare(strict_types=1);

namespace TheCodeFighters\Bundle\AuditorFramework\Common\Types\Domain;

use DateTime;
use DateTimeImmutable;
use ReflectionException;
use TheCodeFighters\Bundle\AuditorFramework\Common\Types\Domain\Event\Event;
use TheCodeFighters\Bundle\AuditorFramework\Common\Types\Domain\Exception\EventNotFoundInTheAggregateRootUnPersistedRecordedEventsException;
use TheCodeFighters\Bundle\AuditorFramework\Common\Utils\FixNullableValueObjectsService;
use function Lambdish\Phunctional\map;

abstract class AggregateRoot
{
    /**
     * @var int
     */
    private $playHead;

    /**
     * @var Id
     */
    protected $id;

    /**
     * @var DateTimeImmutable
     */
    protected $createdAt;

    /**
     * @var DateTime
     */
    protected $updatedAt;

    /**
     * @var Event[]
     */
    protected $unPersistedRecordedEvents;

    /**
     * @var Metadata
     */
    protected $metadata;


    protected function __construct(Id $id)
    {
        $this->playHead = 0;
        $this->unPersistedRecordedEvents = [];
        $this->metadata = new Metadata(
            $id,
            self::class
        );
        parent::__construct();
    }

    /**
     * @return Id
     */
    abstract public function id();

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function playHead(): int
    {
        return $this->playHead;
    }

    public function metadata(): Metadata
    {
        return $this->metadata;
    }


    protected function aggregateId(): string
    {
        return $this->id->value();
    }

    public function recordThat(Event $event): void
    {
        $this->unPersistedRecordedEvents[] = $event;
        parent::recordThat($event);
    }

    public function flushUnPersistedRecordedEvents(): void
    {
        $this->unPersistedRecordedEvents = [];
    }

    /**
     * @return Event[]
     */
    public function unPersistedRecordedEvents(): array
    {
        return $this->unPersistedRecordedEvents;
    }

    /**
     * @param string $eventClassNamespace
     *
     * @return Event
     */
    public function unPersistedRecordedEventByEventClassNamespace(string $eventClassNamespace): Event
    {
        foreach ($this->unPersistedRecordedEvents as $unPersistedRecordedEvent) {
            if ($unPersistedRecordedEvent instanceof $eventClassNamespace) {
                return $unPersistedRecordedEvent;
            }
        }

        throw new EventNotFoundInTheAggregateRootUnpersistedRecordedEventsException($this->id(), $eventClassNamespace);
    }

    public function apply(Event $event): void
    {
        ++$this->playHead;
        $this->version = $this->playHead;
        $this->handleRecursively($event);
    }

    protected function handleRecursively(Event $event): void
    {
        $this->handle($event);
        map(
            function (Entity $entity) use ($event) {
                $entity->registerAggregateRoot($this);
                $entity->handleRecursively($event);
            },
            $this->getChildEntities()
        );

    }

    protected function handle(Event $event): void
    {
        $functionName = 'apply' . $this->getClassNameWithoutNamespace(get_class($event));
        if (method_exists($this, $functionName)) {
            call_user_func([$this, $functionName], $event);
        }
    }

    /**
     * Add all entities contain in the Aggregate as an array. If your aggregate doesn't contain child entities, just
     * return [];.
     *
     * @return Entity[]
     */
    abstract protected function getChildEntities(): array;

    private function getClassNameWithoutNamespace(string $className): string
    {
        $path = explode('\\', $className);

        return array_pop($path);
    }

    /**
     * Needed for Prooph
     */
    public function fixVersionFromReadModel()
    {
        $this->version = $this->playHead;
    }

    /**
     * @throws ReflectionException
     */
    public function fixNullableDoctrineEmbeddables()
    {
        FixNullableValueObjectsService::execute($this);
    }


    //    /**
//     * @throws \Exception
//     */
//    public function encrypt()
//    {
//        foreach ($this->getEncryptedValueObjects() as $encryptedValueObject) {
//            $encryptedValueObject->encrypt();
//        }
//
//    }
//
//    /**
//     * @throws \Exception
//     */
//    public function decrypt()
//    {
//        foreach ($this->getEncryptedValueObjects() as $encryptedValueObject) {
//            $encryptedValueObject->encrypt();
//        }
//    }
//
//    /**
//     * @param Object|null $object
//     * @param EncryptedValueObject[] $immersionParam
//     * @return array
//     * @throws \ReflectionException
//     */
//    private function getEncryptedValueObjects(Object $object = null, array $immersionParam = []): array
//    {
//        if ($object === null) {
//            $object = $this;
//        }
//
//        $encryptedValueObjects = $immersionParam;
//
//        $reflectClass = new \ReflectionClass($object);
//
//        foreach ($reflectClass->getProperties() as $property) {
//            if (is_object($property)) {
//                if (get_class($property) === EncryptedValueObject::class) {
//                    array_push($encryptedValueObjects, $property);
//                }
//                $this->getEncryptedValueObjects($property->,$encryptedValueObjects);
//            }
//        }
//        return $encryptedValueObjects;
//    }

}
