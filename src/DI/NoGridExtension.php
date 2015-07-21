<?php

namespace Carrooi\NoGrid\DI;

use Nette\DI;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NoGridExtension extends DI\CompilerExtension
{


	/** @var array */
	private $defaults = [
		'itemsPerPage' => 10,
		'paginator' => [
			'template' => null,
		],
	];


	public function loadConfiguration()
	{
		$config = $this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$grid = $builder->addDefinition($this->prefix('grid'))
			->setClass('Carrooi\NoGrid\NoGrid')
			->setImplement('Carrooi\NoGrid\INoGridFactory')
			->addSetup('setItemsPerPage', [$config['itemsPerPage']]);

		if ($config['paginator']['template'] !== null) {
			$grid->addSetup('$service->getVisualPaginator()->setTemplatePath(?);', [$config['paginator']['template']]);
		}
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$registerToLatte = function (DI\ServiceDefinition $def) {
			$def->addSetup('?->onCompile[] = function($engine) { Carrooi\NoGrid\Latte\Macros::install($engine->getCompiler()); }', array('@self'));
		};

		$latteFactoryService = $builder->getByType('Nette\Bridges\ApplicationLatte\ILatteFactory') ?: 'nette.latteFactory';
		if ($builder->hasDefinition($latteFactoryService)) {
			$registerToLatte($builder->getDefinition($latteFactoryService));
		}

		if ($builder->hasDefinition('nette.latte')) {
			$registerToLatte($builder->getDefinition('nette.latte'));
		}
	}

}
