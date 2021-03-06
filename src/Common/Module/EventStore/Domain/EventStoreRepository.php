<?php
declare(strict_types=1);

namespace TheCodeFighters\Bundle\AuditorFramework\Common\Module\EventStore\Domain;

use TheCodeFighters\Bundle\AuditorFramework\Common\Types\Domain\AggregateRoot;
use TheCodeFighters\Bundle\AuditorFramework\Common\Types\Domain\Id;

interface EventStoreRepository
{
    public function save(AggregateRoot $aggregateRoot): void;
    public function findAggregateByAggregateId(Id $aggregateRootId): AggregateRoot;
}
