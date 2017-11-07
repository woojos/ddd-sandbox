<?php
namespace Loans\LoanProviding\Domain\Event;

use Money\Currency;
use Money\Money;
use Prooph\EventSourcing\AggregateChanged;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LoanPaidOff extends AggregateChanged
{
    public static function create(
        UuidInterface $id,
        Money $totalAmount
    ) {
        return static::occur(
            $id->toString(),
            [
                'amount' => $totalAmount->getAmount(),
                'currency' => $totalAmount->getCurrency()->getCode(),
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
     * @return Money
     */
    public function money(): Money
    {
        return new Money(
            $this->payload['amount'],
            new Currency($this->payload['currency'])
        );
    }
}