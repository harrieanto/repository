<?php
namespace Repository\Component\View;

use Repository\Component\Contracts\View\ViewInterface;

class Compiler
{
    private $view;

    public function __construct(string $basepath, string $extension, string $target)
    {
        $view = ViewFactory::create();
        $view->setExtension($extension);
        $view->setTargetBasepath($basepath);
        $view->make($target);

        $this->setViewInstance($view);
    }

    public function setViewInstance(ViewInterface $view)
    {
        $this->view = $view;
    }

    public function getViewInstance()
    {
        return $this->view;
    }

    public function getCompiler()
    {
        return $this->view->getCompiler();
    }

    public function compileAsText()
    {
		if ($this->view->isCompilerEnable()) {
            $target = $this->view->getTarget();
            $basepath = $this->view->getTargetBasepath();
            $cacheBasepath = $this->view->getCacheBasepath();

            $compiled = $this->getCompiler()->make($target, $basepath, $cacheBasepath);

			return $compiled->getContent();
		}
    }

    public function compile($keys = array(), $values =  array())
    {
        return $this->view->fetch($keys, $values);
    }
}