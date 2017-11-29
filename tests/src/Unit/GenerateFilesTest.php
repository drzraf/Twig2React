<?php

namespace Twig2React\Test;

use Twig2React\Command\GenerateCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Twig2React\Services\GenerateService;

class GenerateFilesTest extends TestCase {

  protected function setUp()
  {
      if (file_exists(getcwd() . '/tests/example-twig/test.jsx')) {
          unlink(getcwd() . '/tests/example-twig/test.jsx');
      }
  }

	public function testGenerateCommand()
  {

        $generate_service = new GenerateService;
        $fs = new Filesystem;
        $application = new Application('Twig2React', '1.0');
        $application->add(new GenerateCommand($generate_service, $fs));
        $command = $application->find('generate');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(array('y'));
        $commandTester->execute(array(
            'command' => $command->getName(),
            'source' =>  getcwd() . '/tests/example-twig/',
            'destination' => getcwd() . '/tests/example-twig/'
        ));

        $this->assertFileExists(getcwd() . '/tests/example-twig/test.jsx');
    }

    protected function tearDown()
    {
        if (file_exists(getcwd() . '/tests/example-twig/test.jsx')) {
          unlink(getcwd() . '/tests/example-twig/test.jsx');
        }
    }

}
