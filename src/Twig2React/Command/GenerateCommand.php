<?php

namespace Twig2React\Command;

use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Filesystem\Filesystem;
use Twig2React\Services\GenerateService;
use Twig2React\Utility\DirectoryHelper;

class GenerateCommand extends Command {

	protected $file_generator;

	public function __construct(GenerateService $file_generator) {
		parent::__construct();
		$this->file_generator = $file_generator;
	}

	/**
	 * Configure the command options.
	 *
	 * @return void
	 */
	public function configure() {
		$this->setName('generate')
			->setDescription('Generate JSX files from a source.')
			->addArgument('source', InputArgument::OPTIONAL, 'The folder containing target twig templates.')
			->addArgument('destination', InputArgument::OPTIONAL, 'The folder where to output JSX files.');
	}

	public function checkFileSystem($source, $destination) {

		$fileSystem = new Filesystem();

		if (!$fileSystem->exists($source)) {
			throw new RuntimeException('Source file or folder does not exist!');
		}

		if (is_dir($source) && pathinfo($destination, PATHINFO_EXTENSION) && !is_dir($destination)) {
			throw new RuntimeException("Can't generate multiple JSX files to one destination!");
		}

		return $this;

	}

	public function prepareDestination($destination) {

		$filesystem = new Filesystem;

		if (!$filesystem->exists($destination) && !pathinfo($destination, PATHINFO_EXTENSION)) {
			$filesystem->mkdir($destination, 0777);
		}

		try {
			$filesystem->chmod($destination, 0777, 0000, true);
		} catch (IOExceptionInterface $e) {
			$output->writeln('<comment>You should verify that the destination directory are writable.</comment>');
		}

		return $this;

	}

	/**
	 * Execute the command.
	 *
	 * @param  \Symfony\Component\Console\Input\InputInterface  $input
	 * @param  \Symfony\Component\Console\Output\OutputInterface  $output
	 * @return void
	 */
	public function execute(InputInterface $input, OutputInterface $output) {

		$source = ($input->getArgument('source')) ? getcwd() . '/' . $input->getArgument('source') : getcwd();

		$destination = ($input->getArgument('destination')) ? getcwd() . '/' . $input->getArgument('destination') : getcwd();

		$this->checkFileSystem($source, $destination)
			->prepareDestination($destination);

		$target_files = DirectoryHelper::getSourceFiles($source);

		# Prompt user to continue ...
		$helper = $this->getHelper('question');
		$question = new ConfirmationQuestion(sprintf("Found %d twig files in '%s' Continue generating JSX to '%s'? (y/n)", count($target_files), $source, $destination), false);
		if (!$helper->ask($input, $output, $question)) {
			return;
		}

		$output->writeln('<info>Generating JSX ...</info>');

		foreach ($target_files as $target_file) {
			$jsx = $this->file_generator->generateJsx($target_file);
		}

		$output->writeln('<comment>Application ready! Build something amazing.</comment>');

	}

}
