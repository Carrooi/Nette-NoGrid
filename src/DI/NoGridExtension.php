<?php

namespace Carrooi\NoGrid\DI;

use Carrooi\NoGrid\DefaultPaginatorTemplateProvider;
use Carrooi\NoGrid\INoGridFactory;
use Carrooi\NoGrid\Latte\Macros;
use Carrooi\NoGrid\NoGrid;
use Nette\Bridges\ApplicationLatte\ILatteFactory;
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
			'templateProvider' => DefaultPaginatorTemplateProvider::class,
		],
	];

	/** @var string */
	private $paginatorTemplateProviderClass;


	public function loadConfiguration()
	{
		$config = $this->validateConfig($this->defaults);
		$builder = $this->getContainerBuilder();

		$this->paginatorTemplateProviderClass = $config['paginator']['templateProvider'];

		$grid = $builder->addDefinition($this->prefix('grid'))
			->setClass(NoGrid::class)
			->setImplement(INoGridFactory::class)
			->addSetup('setItemsPerPage', [$config['itemsPerPage']]);

		if ($config['paginator']['template'] !== null) {
			$grid->addSetup('$service->getVisualPaginator()->setTemplatePath(?);', [$config['paginator']['template']]);
		}
	}


	public function beforeCompile()
	{
		$builder = $this->getContainerBuilder();

		$grid = $builder->getDefinition($this->prefix('grid'));

		$paginatorTemplateProvider = $builder->getByType($this->paginatorTemplateProviderClass);
		if (!$paginatorTemplateProvider) {
			$paginatorTemplateProvider = $this->prefix('paginatorTemplateProvider');
			$builder->addDefinition($paginatorTemplateProvider)
				->setClass($this->paginatorTemplateProviderClass);
		}

		$grid->addSetup('?->_setPaginatorTemplateProvider($this->getService(\''. $paginatorTemplateProvider. '\'))', ['@self']);

		$registerToLatte = function (DI\ServiceDefinition $def) {
			$def->addSetup('?->onCompile[] = function($engine) { '. Macros::class. '::install($engine->getCompiler()); }', ['@self']);
		};

		$latteFactoryService = $builder->getByType(ILatteFactory::class) ?: 'nette.latteFactory';
		if ($builder->hasDefinition($latteFactoryService)) {
			$registerToLatte($builder->getDefinition($latteFactoryService));
		}

		if ($builder->hasDefinition('nette.latte')) {
			$registerToLatte($builder->getDefinition('nette.latte'));
		}
	}

}
