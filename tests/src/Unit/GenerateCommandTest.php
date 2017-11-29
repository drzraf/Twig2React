<?php

namespace Twig2React\Test;

use Twig2React\Command\GenerateCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Twig2React\Services\GenerateService;

class GenerateCommandTest extends TestCase {

	public function testGenerateCommand(){

        $command = new GenerateCommand;
        $generate_service = new GenerateService;
        $fs = new Filesystem;
        $application = new Application('Twig2React', '1.0');
        $application->add(new GenerateCommand($generate_service, $fs));
        $command = $application->find('generate');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(array('n'));
        $commandTester->execute(array(
            'command' => $command->getName()
        ));

        $this->assertRegExp('/User cancelled./', $commandTester->getDisplay());

    }

}
