<?php

namespace Carrooi\NoGrid;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class DefaultPaginatorTemplateProvider implements IPaginatorTemplateProvider
{


	/**
	 * @return string
	 */
	public function getTemplatePath()
	{
		return __DIR__. '/templates/paginator.latte';
	}

}
