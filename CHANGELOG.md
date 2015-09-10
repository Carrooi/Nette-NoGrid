# Change log

## [Unreleased](https://github.com/Carrooi/Nette-NoGrid/compare/1.1.0...master)
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
