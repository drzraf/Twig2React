<?php

namespace Twig2React\Services;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\DomCrawler\Crawler;

class GenerateService {

  protected $template;

  protected function jsxTemplate()
  {
      $this->template = <<<EOT
    import React from 'react';\n
    const render = function() {
        return (
          {$this->template}
        );
    };\n
    export default render;
EOT;

     return $this;

  }

  /**
   * Transform twig into JSX
   *
   * @return string
   */
    public function generateJsx($path)
    {

		$file = new File($path);

        $this->template = file_get_contents($file->getPathname());

        $this->transform()
            ->jsxTemplate();

        return $this->template;

    }

    protected function transform()
    {

        # Convert double braces to single
        $this->template = preg_replace('/{{([\[\]a-zA-Z0-9 ._\'\"]+)}}/', '{$1}', $this->template);

        # Convert class names without variables
        $this->template = preg_replace('/class="([^{}]+)"/U', 'className="$1"', $this->template);

        # Convert class names that are variables
        $this->template = preg_replace('/class="{(.*)}"/U', 'className={$1}', $this->template);

        # Convert src attributes that are variables
        $this->template = preg_replace('/src="{(.*)}"/U', 'src={$1}', $this->template);

        # Convert id attributes that are variables
        $this->template = preg_replace('/id="{(.*)}"/U', 'id={$1}', $this->template);

        # Convert href attributes that are variables
        $this->template = preg_replace('/href="{(.*)}"/U', 'href={$1}', $this->template);

        # Convert data attributes
        $this->template = preg_replace('/data-([a-zA-Z]+)="{(.*)}"/U', 'data-$1={$2}', $this->template);

        return $this;

    }


}
