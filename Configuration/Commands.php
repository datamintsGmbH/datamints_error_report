<?php
return [
    'error-report:report:send' => [
        'class' => \Datamints\DatamintsErrorReport\Command\Report\SendCommand::class,
        'schedulable' => true,
    ],
    'error-report:dispatch' => [
        'class' => \Datamints\DatamintsErrorReport\Command\DispatchCommand::class,
        'schedulable' => true,
    ],
];
