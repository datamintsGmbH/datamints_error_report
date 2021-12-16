<?php
return [
    'error-report:report:send' => [
        'class' => \Datamints\DatamintsErrorReport\Command\Report\SendCommand::class,
        'schedulable' => true,
    ],
];
