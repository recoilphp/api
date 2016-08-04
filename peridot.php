<?php

declare (strict_types = 1); // @codeCoverageIgnore

use Evenement\EventEmitterInterface;
use Peridot\Console\Environment;
use Peridot\Reporter\CodeCoverage\AbstractCodeCoverageReporter;
use Peridot\Reporter\CodeCoverageReporters;
use Recoil\Peridot\RecoilTestExecutor;

require __DIR__ . '/vendor/autoload.php';

return new RecoilTestExecutor(
    function (EventEmitterInterface $emitter) {
        (new CodeCoverageReporters($emitter))->register();

        $emitter->on('peridot.start', function (Environment $environment) {
            $environment->getDefinition()->getArgument('path')->setDefault('test/suite');
        });

        $emitter->on('code-coverage.start', function (AbstractCodeCoverageReporter $reporter) {
            $reporter->addDirectoryToWhitelist(__DIR__ . '/src');
        });
    }
);
