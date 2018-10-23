# Jane


[![Latest Version](https://img.shields.io/github/release/jolicode/jane.svg?style=flat-square)](https://github.com/jolicode/jane/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Build Status](https://img.shields.io/travis/jolicode/jane.svg?style=flat-square)](https://travis-ci.org/jolicode/jane)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/jolicode/jane.svg?style=flat-square)](https://scrutinizer-ci.com/g/jolicode/jane)
[![Quality Score](https://img.shields.io/scrutinizer/g/jolicode/jane.svg?style=flat-square)](https://scrutinizer-ci.com/g/jolicode/jane)
[![Total Downloads](https://img.shields.io/packagist/dt/jane/jane.svg?style=flat-square)](https://packagist.org/packages/jane/jane)

Jane is a library to generate, in PHP, a model and a serializer from a [JSON Schema](http://json-schema.org/).

## Disclaimer

The generated code may contain bug or incorrect behavior, use it as a base for your application but you should never trust it as is.

## Usage

```
# jane [schema-path] [root name] [namespace] [destination]
php vendor/bin/jane generate json-schema.json Classname Namespace\\Prefix src/Namespace/Prefix
```

This will generate, in the `src/Namespace/Prefix`, a `Model` and a `Normalizer` directory.

`Model` directory will contain a `Namespace\Prefix\Model\Classname`, which correspond to the root Schema
of the json schema file, and all the subclasses discovered through the parsing of the Schema.

`Normalizer` directory will contain a normalizer service class for each of the model class generated.

## Installation

Use composer for installation

```
composer require jane/jane
```

## Recommended workflow

Here is a recommended workflow when dealing with the generated code:

 1. Start from a clean revision on your project (no modified files);
 2. Update your Json Schema file (edit or download new version);
 3. Generate the new code;
 4. Check the generated code with a diff tool: `git diff` for example;
 5. If all is well commit modifications.

An optional and recommanded practice is to separate the generated code in a specific directory
like creating a `generated` directory in your project and using jane inside. This allows other developers
to be aware that this part of the project is generated and must not be updated manually.

[See this library (jane-swagger)](https://github.com/jolicode/jane-swagger) for an example on how to achieve that.

## Internal

Here is a quick presentation on how this library transforms a Json Schema file into models and normalizers:

 1. First step is to read and parse the Json Schema file;
 2. Second step is to guess classes and their associated properties and types;
 3. Once all things are guessed, classes and their properties are transformed into an AST (by using the [PHP-Parser library from nikic](https://github.com/nikic/PHP-Parser));
 4. Then the AST is written into PHP files.

## Credits

* [All contributors](https://github.com/jolicode/jane/graphs/contributors)

## License

View the [LICENSE](LICENSE) file attach to this project.
