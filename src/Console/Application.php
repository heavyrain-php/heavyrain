<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Console;

use Symfony\Component\Console\Application as SymfonyApplication;

final class Application extends SymfonyApplication
{
    private const NAME = 'heavyrain';

    private const VERSION = '0.0.1';

    public function __construct()
    {
        parent::__construct(self::NAME, self::VERSION);
        $this->addCommands([
            new Commands\GenerateStubCommand(),
            new Commands\RunCommand(),
            new Commands\WebCommand(),
        ]);
    }
}
