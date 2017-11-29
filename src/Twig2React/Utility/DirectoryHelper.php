<?php

namespace Twig2React\Utility;

use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\File\File;

class DirectoryHelper {

	public static function getFileName($source)
	{
		$file = new File($source);
		
		return $file->getFilename();
	}

	public static function getFileNameNoExtension($source)
	{
		$file_name = self::getFileName($source);
		$arr = explode(".", $file_name, 2);
		
		return $arr[0];
	}

	public static function getSourceFiles($source)
	{

		$finder = new Finder();

		$targetFiles = [];

		if (is_dir($source)) {
			$finder->files()->in($source);
			foreach ($finder as $file) {
				if ($file->getExtension() === "twig") {
					$targetFiles[] = $file->getRealPath();
				}
			}
		} else {
			$file = new File($source);
			if ($file->getExtension() === "twig") {
				$targetFiles[] = $source;
			}
		}

		return $targetFiles;
	}

}