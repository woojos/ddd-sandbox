<?php
namespace Loans\LoanProviding\Domain\Event;

use Prooph\EventSourcing\AggregateChanged;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LoanCanceled extends AggregateChanged
{
    public static function create(
        UuidInterface $id,
        string $reason
    ) {
        return static::occur(
            $id->toString(),
            [
                'reason' => $reason,
            ]
        );
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function id()
    {
        return Uuid::fromString($this->aggregateId());
    }

    /**
     * @return mixed
     */
    public function reason()
    {
        return $this->payload['reason'];
    }
}