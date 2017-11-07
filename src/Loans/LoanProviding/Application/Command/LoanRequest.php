<?php
namespace Loans\LoanProviding\Application\Command;

use Money\Currency;
use Money\Money;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LoanRequest extends Command
{
    use PayloadTrait;

    /**
     * LoanRequest constructor.
     * @param UuidInterface $loanId
     * @param UuidInterface $customerId
     * @param Money $money
     * @param \DateTimeImmutable $dateTo
     */
    public function __construct(UuidInterface $loanId, UuidInterface $customerId, Money $money, \DateTimeImmutable $dateTo)
    {
        $this->init();
        $this->setPayload([
            'loan_id' => $loanId->toString(),
            'customer_id' => $customerId->toString(),
            'amount' => $money->getAmount(),
            'currency' => $money->getCurrency()->getCode(),
            'date_to' => $dateTo,
        ]);
    }

    /**
     * @return \Ramsey\Uuid\UuidInterface
     */
    public function loanId()
    {
        return Uuid::fromString($this->payload['loan_id']);
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
    public function money(): Money
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
        return $this->payload['date_to'];
    }
}