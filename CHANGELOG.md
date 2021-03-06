# Change log

## [2.0.2](https://github.com/Carrooi/Nette-NoGrid/compare/2.0.1...2.0.2)
* Fix typo

## [2.0.1](https://github.com/Carrooi/Nette-NoGrid/compare/2.0.0...2.0.1)
* Fix nette DI extension

## [2.0.0](https://github.com/Carrooi/Nette-NoGrid/compare/1.1.0...2.0.0)
* Update to nette 2.4
* Many fixes and refactoring
* Add support for filters
* Add more options to doctrine data sources
* Add support for DoctrineQueryFunction [#18](https://github.com/Carrooi/Nette-NoGrid/issues/18)

## [1.1.0](https://github.com/Carrooi/Nette-NoGrid/compare/1.0.0...1.1.0)
* Add `no-grid-empty`, `no-grid-not-empty`, `no-grid-has-paginator` latte macros
* Doctrine data source: use Paginator for fetching data
* Macros: add possibility to use `$iterator` in `no-grid-data-as` and `no-grid-views-as` latte macros
* Add `DoctrineQueryObjectDataSource` data source
* Allow last version of kdyby/doctrine
* Add option to disable paginator
* Add camelCased variants of all latte macros
* Refactored views
* Add tests
* Add option to transform loaded data
* Add option to set paginator template provider service

## 1.0.0
* Initial version
