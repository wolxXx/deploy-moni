<?php

declare(strict_types=1);


return \Rector\Config\RectorConfig::configure()
                   ->withPaths([
                                   __DIR__ . '/app',
                               ])
                   ->withTypeCoverageLevel(0)
                   ->withDeadCodeLevel(0)
                   ->withCodeQualityLevel(0)
                   ->withRules([
                                   \SavinMikhail\AddNamedArgumentsRector\AddNamedArgumentsRector::class,
                               ])
;
