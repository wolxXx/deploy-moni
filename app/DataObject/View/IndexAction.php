<?php

declare(strict_types=1);

namespace Application\DataObject\View;

class IndexAction
{
    public function __construct(
        public array              $groups,
        public \IntlDateFormatter $dateFormatter,
    ) {
    }
}
