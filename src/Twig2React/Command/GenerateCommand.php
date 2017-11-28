<?php

namespace Twig2React\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Twig2React\Services\FileService;

class GenerateCommand extends Command {

  private $fileGenerator;

  public function __construct(FileService $file_generator)
  {
    parent::__construct();
    $this->fileGenerator = $file_generator;
  }

  /**
   * Configure the command options.
   *
   * @return void
   */
  public function configure()
  {
    $this->setName('generate')
         ->setDescription('Generate JSX files from a source.')
         ->addArgument('source', InputArgument::OPTIONAL, 'The folder containing target twig templates.')
         ->addArgument('destination', InputArgument::OPTIONAL, 'The folder where to output JSX files.');
  }

  /**
   * Execute the command.
   *
   * @param  \Symfony\Component\Console\Input\InputInterface  $input
   * @param  \Symfony\Component\Console\Output\OutputInterface  $output
   * @return void
   */
  public function execute(InputInterface $input, OutputInterface $output)
  {

    $fileSystem = new Filesystem();

    $finder = new Finder();

    $source = ($input->getArgument('source')) ? getcwd().'/'.$input->getArgument('source') : getcwd();

    $destination = ($input->getArgument('destination')) ? getcwd().'/'.$input->getArgument('destination') : getcwd();

    if(!$fileSystem->exists($source)) {
      throw new RuntimeException('Source file or folder does not exist!');
    }

    $targetFiles = [];

    if(is_dir($source)) {
      $finder->files()->in($source);
      foreach ($finder as $file) {
          var_dump($file->getRealPath());
      }
    } else {
      $targetFiles[] = $source;
    }

    $output->writeln('<info>Generating JSX ...</info>');

    $output->writeln('<comment>Application ready! Build something amazing.</comment>');

  }

}
