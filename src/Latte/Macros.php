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

		$me->addMacro('no-grid', [$me, 'macroNoGrid'], 'unset($_noGrid, $noGrid);');
		$me->addMacro('no-grid-data-as', [$me, 'macroNoGridDataAs'], '}');
		$me->addMacro('no-grid-views-as', [$me, 'macroNoGridViewsAs'], '}');
		$me->addMacro('no-grid-empty', [$me, 'macroNoGridEmpty'], '}');
		$me->addMacro('no-grid-not-empty', [$me, 'macroNoGridNotEmpty'], '}');

		return $me;
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroNoGrid(MacroNode $node, PhpWriter $writer)
	{
		if ($this->findParentMacro($node, 'no-grid')) {
			throw new MacroDefinitionException('Nesting no-grid macros is not allowed.');
		}

		return $writer->write('$_noGrid = $noGrid = $_control[%node.word];');
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroNoGridDataAs(MacroNode $node, PhpWriter $writer)
	{
		if (!$this->findParentMacro($node, 'no-grid')) {
			throw new MacroDefinitionException('Macro no-grid-data-as must be inside of no-grid macro.');
		}

		return $writer->write('foreach ($_noGrid->getData() as %node.word) {');
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroNoGridViewsAs(MacroNode $node, PhpWriter $writer)
	{
		if (!$this->findParentMacro($node, 'no-grid')) {
			throw new MacroDefinitionException('Macro no-grid-views-as must be inside of no-grid macro.');
		}

		return $writer->write('foreach ($_noGrid->getViews() as %node.word) {');
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param \Latte\PhpWriter $writer
	 * @return string
	 */
	public function macroNoGridEmpty(MacroNode $node, PhpWriter $writer)
	{
		if (!$this->findParentMacro($node, 'no-grid')) {
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
		if (!$this->findParentMacro($node, 'no-grid')) {
			throw new MacroDefinitionException('Macro no-grid-not-empty must be inside of no-grid macro.');
		}

		return $writer->write('if ($_noGrid->getCount() > 0) {');
	}


	/**
	 * @param \Latte\MacroNode $node
	 * @param string $name
	 * @return bool
	 */
	private function findParentMacro(MacroNode $node, $name)
	{
		$current = $node;
		while ($current = $current->parentNode) {
			if ($current->name === $name) {
				return true;
			}
		}

		return false;
	}

}
