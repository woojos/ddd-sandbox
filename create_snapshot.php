<?php

use Loans\LoanProviding\Domain\Loan;
use Prooph\EventSourcing\Aggregate\AggregateType;
use Prooph\EventSourcing\EventStoreIntegration\AggregateTranslator;
use Prooph\Snapshotter\SnapshotReadModel;
use Prooph\Snapshotter\StreamSnapshotProjection;

require_once 'setup.php';


$projection = $projectionManager->createReadModelProjection(
    'loans-snapshots',
    new SnapshotReadModel(
        $aggregateRepository,
        new AggregateTranslator(),
        $snapshotStore,
        [
            AggregateType::fromAggregateRootClass(Loan::class)->toString()
        ]
    )
);


$streamSnapshotProjection = new StreamSnapshotProjection($projection, $streamName->toString());
$streamSnapshotProjection(false);