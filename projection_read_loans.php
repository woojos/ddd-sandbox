<?php

use Loans\LoanProviding\Domain\Event\LoanCreated;
use Loans\LoanProviding\Domain\Event\LoanPaidOff;
use Loans\LoanProviding\Domain\LoanStatus;

require_once __DIR__ . '/setup.php';

$projection = $projectionManager->createProjection('read_loans');
$projection
    ->fromAll()
    ->whenAny(function ($state, $event) use ($connection) {
        switch (get_class($event)) {
            case LoanCreated::class:
                $stmt = $connection->prepare(
                    'INSERT INTO read_loans (id, amount, remaining, currency, status) VALUES (:id, :amount, :remaining, :currency, :status)'
                );
                /** @var LoanCreated $event */
                $stmt->execute([
                    ':id' => $event->id()->toString(),
                    ':amount' => $event->totalAmount()->getAmount(),
                    ':remaining' => $event->totalAmount()->getAmount(),
                    ':currency' => $event->totalAmount()->getCurrency()->getCode(),
                    ':status' => LoanStatus::ACTIVE
                ]);
                break;
            case LoanPaidOff::class:
                /** @var LoanPaidOff $event */
                $stmt = $connection->prepare('UPDATE read_loans SET remaining = remaining - :amount WHERE id = :id');
                $stmt->execute([
                    ':amount' => $event->money()->getAmount(),
                    ':id' => $event->id()->toString()
                ]);
                break;
        }
    })
    ->run(false);