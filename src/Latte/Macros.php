<?php

namespace Carrooi\NoGrid\Latte;

use Carrooi\NoGrid\MacroDefinitionException;
use Latte\Compiler;
use Latte\MacroNode;
use Latte\Macros\MacroSet;
use Latte\PhpWriter;

/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class Macros extends MacroSet
{


	/**
	 * @param \Latte\Compiler $compiler
	 * @return static
	 */
	public static function install(Compiler $compiler)
	{
		$me = new static($compiler);

		$me->addMacro('no-grid', [$me, 'macroNoGrid'], [$me, 'macroNoGridEnd']);
		$me->addMacro('no-grid-data-as', '', [$me, 'macroNoGridDataAs']);
		$me->addMacro('no-grid-views-as', '', [$me, 'macroNoGridViewsAs']);
		$me->addMacro('no-grid-empty', [$me, 'macroNoGridEmpty'], '}');
		$me->addMacro('no-grid-not-empty', [$me, 'macroNoGridNotEmpty'], '}');
		$me->addMacro('no-grid-has-paginator', [$me, 'macroNoGridHasPaginator'], '}');

		$me->addMacro('noGrid', [$me, 'macroNoGrid'], [$me, 'macroNoGridEnd']);
		$me->addMacro('noGridDataAs', '', [$me, 'macroNoGridDataAs']);
		$me->addMacro('noGridViewsAs', '', [$me, 'macroNoGridViewsAs']);
		$me->addMacro('noGridEmpty', [$me, 'macroNoGridEmpty'], '}');
		$me->addMacro('noGridNotEmpty', [$me, 'macroNoGridNotEmpty'], '}');
		$me->addMacro('noGridHasPaginator', [$me, 'macroNoGridHasPaginator'], '}');

		return $me;
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroNoGrid(MacroNode $node, PhpWriter $writer)
	{
		if ($this->isInGrid($node)) {
			throw new MacroDefinitionException('Nesting no-grid macros is not allowed.');
		}

		return $writer->write(
			'$_noGrid = $noGrid = $_control[%node.word]; '.
			'if ($_noGrid->hasFilteringForm()) { '.
				'echo Nette\Bridges\FormsLatte\Runtime::renderFormBegin($form = $_form = $_noGrid["filteringForm"], []); '.
			'} '
		);
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroNoGridEnd(MacroNode $node, PhpWriter $writer)
	{
		return $writer->write(
			'if ($_noGrid->hasFilteringForm()) { '.
				'echo Nette\Bridges\FormsLatte\Runtime::renderFormEnd($_form); '.
			'} '.
			'unset($_noGrid, $noGrid);'
		);
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroNoGridDataAs(MacroNode $node, PhpWriter $writer)
	{
		if (!$this->isInGrid($node)) {
			throw new MacroDefinitionException('Macro no-grid-data-as must be inside of no-grid macro.');
		}

		$this->createIterator($node, $writer, '$_noGrid->getData()');
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroNoGridViewsAs(MacroNode $node, PhpWriter $writer)
	{
		if (!$this->isInGrid($node)) {
			throw new MacroDefinitionException('Macro no-grid-views-as must be inside of no-grid macro.');
		}

		$this->createIterator($node, $writer, '$_noGrid->getViews()');
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroNoGridEmpty(MacroNode $node, PhpWriter $writer)
	{
		if (!$this->isInGrid($node)) {
			throw new MacroDefinitionException('Macro no-grid-empty must be inside of no-grid macro.');
		}

		return $writer->write('if ($_noGrid->getCount() === 0) {');
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroNoGridNotEmpty(MacroNode $node, PhpWriter $writer)
	{
		if (!$this->isInGrid($node)) {
			throw new MacroDefinitionException('Macro no-grid-not-empty must be inside of no-grid macro.');
		}

		return $writer->write('if ($_noGrid->getCount() > 0) {');
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroNoGridHasPaginator(MacroNode $node, PhpWriter $writer)
	{
		if (!$this->isInGrid($node)) {
			throw new MacroDefinitionException('Macro no-grid-not-empty must be inside of no-grid macro.');
		}

		return $writer->write('if ($_noGrid->isPaginatorEnabled() && $_noGrid->getTotalCount() > $_noGrid->getCount()) {');
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @return bool
	 */
	private function isInGrid(MacroNode $node)
	{
		return $this->findParentMacro($node, ['no-grid', 'noGrid']);
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param array $name
	 * @return bool
	 */
	private function findParentMacro(MacroNode $node, array $name)
	{
		$current = $node;
		while ($current = $current->parentNode) {
			if (in_array($current->name, $name)) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @from https://github.com/nette/latte/blob/master/src/Latte/Macros/CoreMacros.php#L253-L263
	 *
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @param string $iterate
	 */
	private function createIterator(MacroNode $node, PhpWriter $writer, $iterate)
	{
		if ($node->modifiers !== '|noiterator' && preg_match('#\W(\$iterator|include|require|get_defined_vars)\W#', $this->getCompiler()->expandTokens($node->content))) {
			$node->openingCode = '<?php $iterations = 0; foreach ($iterator = $_l->its[] = new Latte\Runtime\CachingIterator('. $iterate. ') as '. $writer->formatArgs(). ') { ?>';
			$node->closingCode = '<?php $iterations++; } array_pop($_l->its); $iterator = end($_l->its) ?>';
		} else {
			$node->openingCode = '<?php $iterations = 0; foreach ('. $iterate. ' as ' . $writer->formatArgs() . ') { ?>';
			$node->closingCode = '<?php $iterations++; } ?>';
		}
	}

}
