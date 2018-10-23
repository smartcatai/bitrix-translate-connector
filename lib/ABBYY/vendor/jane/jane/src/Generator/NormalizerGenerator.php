<?php

namespace Joli\Jane\Generator;

use Joli\Jane\Generator\Context\Context;
use Joli\Jane\Generator\Normalizer\DenormalizerGenerator;
use Joli\Jane\Generator\Normalizer\NormalizerGenerator as NormalizerGeneratorTrait;

use Joli\Jane\Model\JsonSchema;

use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt;
use PhpParser\Node\Scalar;

class NormalizerGenerator implements GeneratorInterface
{
    const FILE_TYPE_NORMALIZER = 'normalizer';

    use DenormalizerGenerator;
    use NormalizerGeneratorTrait;

    /**
     * @var Naming The naming service
     */
    protected $naming;

    /**
     * @param Naming $naming Naming Service
     */
    public function __construct(Naming $naming)
    {
        $this->naming = $naming;
    }

    /**
     * The naming service
     *
     * @return Naming
     */
    protected function getNaming()
    {
        return $this->naming;
    }

    /**
     * Generate a set of files given a schema
     *
     * @param mixed   $schema    Schema to generate from
     * @param string  $className Class to generate
     * @param Context $context   Context for generation
     *
     * @return File[]
     */
    public function generate($schema, $className, Context $context)
    {
        $files   = [];
        $classes = [];

        foreach ($context->getObjectClassMap() as $class) {
            $methods   = [];
            $modelFqdn = $context->getNamespace()."\\Model\\".$class->getName();
            $methods[] = $this->createSupportsDenormalizationMethod($modelFqdn);
            $methods[] = $this->createSupportsNormalizationMethod($modelFqdn);
            $methods[] = $this->createDenormalizeMethod($modelFqdn, $context, $class->getProperties());
            $methods[] = $this->createNormalizeMethod($modelFqdn, $context, $class->getProperties());

            $normalizerClass = $this->createNormalizerClass(
                $class->getName().'Normalizer',
                $methods
            );
            $classes[] = $normalizerClass->name;

            $namespace = new Stmt\Namespace_(new Name($context->getNamespace()."\\Normalizer"), [
                new Stmt\Use_([new Stmt\UseUse(new Name('Joli\Jane\Reference\Reference'))]),
                new Stmt\Use_([new Stmt\UseUse(new Name('Symfony\Component\Serializer\Normalizer\DenormalizerInterface'))]),
                new Stmt\Use_([new Stmt\UseUse(new Name('Symfony\Component\Serializer\Normalizer\NormalizerInterface'))]),
                new Stmt\Use_([new Stmt\UseUse(new Name('Symfony\Component\Serializer\Normalizer\SerializerAwareNormalizer'))]),
                $normalizerClass
            ]);
            $files[]   = new File($context->getDirectory().'/Normalizer/'.$class->getName().'Normalizer.php', $namespace, self::FILE_TYPE_NORMALIZER);
        }

        $files[] = new File(
            $context->getDirectory().'/Normalizer/NormalizerFactory.php',
            new Stmt\Namespace_(new Name($context->getNamespace()."\\Normalizer"), [
                new Stmt\Use_([new Stmt\UseUse(new Name('Joli\Jane\Normalizer\ReferenceNormalizer'))]),
                new Stmt\Use_([new Stmt\UseUse(new Name('Joli\Jane\Normalizer\NormalizerArray'))]),
                $this->createNormalizerFactoryClass($classes)
            ]),
            self::FILE_TYPE_NORMALIZER
        );

        return $files;
    }

    protected function createNormalizerFactoryClass($classes)
    {
        $statements = [
            new Expr\Assign(new Expr\Variable('normalizers'), new Expr\Array_()),
            new Expr\Assign(new Expr\ArrayDimFetch(new Expr\Variable('normalizers')), new Expr\New_(new Name('ReferenceNormalizer'))),
            new Expr\Assign(new Expr\ArrayDimFetch(new Expr\Variable('normalizers')), new Expr\New_(new Name('NormalizerArray')))
        ];

        foreach ($classes as $class) {
            $statements[] = new Expr\Assign(new Expr\ArrayDimFetch(new Expr\Variable('normalizers')), new Expr\New_($class));
        }

        $statements[] = new Stmt\Return_(new Expr\Variable('normalizers'));

        return new Stmt\Class_('NormalizerFactory', [
            'stmts' => [
                new Stmt\ClassMethod('create', [
                    'type' => Stmt\Class_::MODIFIER_STATIC | Stmt\Class_::MODIFIER_PUBLIC,
                    'stmts' => $statements
                ])
            ]
        ]);
    }
}
