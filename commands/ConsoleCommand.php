<?php

namespace Commands;

use Exception;

interface ConsoleCommand {
    /**
     * Console command's name getter.
     * @return string Command's name.
     */
    function getName(): string;

    /**
     * Console command's description getter.
     * @return string Command's description.
     */
    function getDescription(): string;

    /**
     * Runs the command.
     * @param array $args Arguments.
     * @throws Exception
     */
    function run(array $args): void;
}