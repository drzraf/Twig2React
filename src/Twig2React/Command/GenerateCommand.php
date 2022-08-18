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

	/**
	 * Twig2React\Services\GenerateService
	 */
	protected $file_generator;

	/**
	 * Symfony\Component\Filesystem\Filesystem
	 */
	protected $file_system;

	/**
	 * construct
	 *
	 * @param Twig2React\Services\GenerateService $file_generator
	 * @param Symfony\Component\Filesystem\Filesystem $file_system
	 */
	public function __construct(GenerateService $file_generator, Filesystem $file_system)
	{
		parent::__construct();
		$this->file_generator = $file_generator;
		$this->file_system = $file_system;
	}

	/**
	 * Configure the command
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
	 * Check the input and outputs are valid
	 *
	 * @param string $input
	 * @param string $output
	 */
	public function checkFileSystem($source, $destination)
	{

		if (!$this->file_system->exists($source)) {
			throw new RuntimeException('Source file or folder does not exist!');
		}

		if (is_dir($source) && pathinfo($destination, PATHINFO_EXTENSION) && !is_dir($destination)) {
			throw new RuntimeException("Can't generate multiple JSX files to one destination!");
		}

		return $this;

	}

	protected function getIntendedPath(InputInterface $user_input, $parameter)
	{

		$path = getcwd();

		if($user_input->getArgument($parameter)) {
			$path .= '/' . $user_input->getArgument($parameter);
			if (substr($user_input->getArgument($parameter), 0, 1) === '/') {
				$path = $user_input->getArgument($parameter);
			}
		}

		return $path;

	}

	/**
	 * Create the destination dir
	 *
	 * @param string $destination
	 */
	public function prepareDestination($destination)
	{

		if (!$this->file_system->exists($destination) && !pathinfo($destination, PATHINFO_EXTENSION)) {
			$this->file_system->mkdir($destination, 0777);
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
	public function execute(InputInterface $input, OutputInterface $output): int
	{

		$source	= $this->getIntendedPath($input, 'source');

		$destination = $this->getIntendedPath($input, 'destination');

		$this->checkFileSystem($source, $destination)
			->prepareDestination($destination);

		$target_files = DirectoryHelper::getSourceFiles($source);

		# Prompt user to continue ...
		$helper = $this->getHelper('question');
		$question = new ConfirmationQuestion(sprintf("Found %d twig files in '%s' Continue generating JSX to '%s'? (y/n)", count($target_files), $source, $destination), false);
		if (!$helper->ask($input, $output, $question)) {
			$output->writeln('<info>User cancelled.</info>');
			return 1;
		}

		$output->writeln('<info>Generating JSX ...</info>');

		foreach ($target_files as $target_file) {
			if(pathinfo($destination, PATHINFO_EXTENSION) && !is_dir($destination)) {
				$file_name = $destination;
			} else {
				$file_name = $destination . '/' . DirectoryHelper::getFileNameNoExtension($target_file) . '.jsx';
			}
			$jsx = $this->file_generator->generateJsx($target_file, $source);
			$this->file_system->dumpFile($file_name, $jsx);
		}

		$output->writeln('<comment>Application ready! Build something amazing.</comment>');

    return 0;
	}
}
