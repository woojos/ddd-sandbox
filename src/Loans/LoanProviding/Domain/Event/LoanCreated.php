<?php
namespace Loans\LoanProviding\Domain\Event;

use Money\Currency;
use Money\Money;
use Prooph\EventSourcing\AggregateChanged;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LoanCreated extends AggregateChanged
{
    public static function create(
        UuidInterface $id,
        UuidInterface $customerId,
        Money $totalAmount,
        \DateTimeImmutable $dateTo
    ) {
        return static::occur(
            $id->toString(),
            [
                'customer_id' => $customerId->toString(),
                'amount' => $totalAmount->getAmount(),
                'currency' => $totalAmount->getCurrency()->getCode(),
                'date_to' => $dateTo->format(DATE_ATOM)
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
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function customerId()
    {
        return Uuid::fromString($this->payload['customer_id']);
    }

    /**
     * @return Money
     */
    public function totalAmount(): Money
    {
        return new Money(
            $this->payload['amount'],
            new Currency($this->payload['currency'])
        );
    }

    /**
     * @return \DateTimeImmutable
     */
    public function dateTo(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->payload['date_to']);
    }
}