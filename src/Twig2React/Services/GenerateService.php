<?php

namespace Twig2React\Services;

use Symfony\Component\HttpFoundation\File\File;

class GenerateService {

	public function generateJsx($path) {
		$file = new File($path);
		var_dump($file->getContents());
		die();
	}
}
