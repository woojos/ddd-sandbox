<?php
namespace Loans\LoanProviding\Application\Command;

use Money\Currency;
use Money\Money;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadTrait;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LoanRepay extends Command
{
    use PayloadTrait;

    /**
     * @param UuidInterface $loanId
     * @param Money $amount
     */
    public function __construct(UuidInterface $loanId, Money $amount)
    {
        $this->init();
        $this->setPayload([
            'loan_id' => $loanId->toString(),
            'amount' => $amount->getAmount(),
            'currency' => $amount->getCurrency()->getCode(),
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