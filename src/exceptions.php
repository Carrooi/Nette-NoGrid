<?php

namespace Carrooi\NoGrid;


/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class LogicException extends \LogicException {}


/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class MacroDefinitionException extends LogicException {}


/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class InvalidArgumentException extends LogicException {}


/**
 *
 * @author David Kudera <kudera.d@gmail.com>
 */
class NotImplementedException extends LogicException {}


/**
 *
 * @author Martin Janeček <admin@ikw.cz>
 */
class InvalidStateException extends \RuntimeException {}
