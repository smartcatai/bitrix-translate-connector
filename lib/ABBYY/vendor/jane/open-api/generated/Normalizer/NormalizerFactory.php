<?php

namespace Joli\Jane\OpenApi\Normalizer;

use Joli\Jane\Normalizer\NormalizerArray;
use Joli\Jane\Normalizer\ReferenceNormalizer;

class NormalizerFactory
{
    public static function create()
    {
        $normalizers   = [];
        $normalizers[] = new ReferenceNormalizer();
        $normalizers[] = new NormalizerArray();
        $normalizers[] = new OpenApiNormalizer();
        $normalizers[] = new InfoNormalizer();
        $normalizers[] = new ContactNormalizer();
        $normalizers[] = new LicenseNormalizer();
        $normalizers[] = new ExternalDocsNormalizer();
        $normalizers[] = new OperationNormalizer();
        $normalizers[] = new PathItemNormalizer();
        $normalizers[] = new ResponseNormalizer();
        $normalizers[] = new HeaderNormalizer();
        $normalizers[] = new BodyParameterNormalizer();
        $normalizers[] = new HeaderParameterSubSchemaNormalizer();
        $normalizers[] = new FormDataParameterSubSchemaNormalizer();
        $normalizers[] = new QueryParameterSubSchemaNormalizer();
        $normalizers[] = new PathParameterSubSchemaNormalizer();
        $normalizers[] = new SchemaNormalizer();
        $normalizers[] = new FileSchemaNormalizer();
        $normalizers[] = new PrimitivesItemsNormalizer();
        $normalizers[] = new XmlNormalizer();
        $normalizers[] = new TagNormalizer();
        $normalizers[] = new BasicAuthenticationSecurityNormalizer();
        $normalizers[] = new ApiKeySecurityNormalizer();
        $normalizers[] = new Oauth2ImplicitSecurityNormalizer();
        $normalizers[] = new Oauth2PasswordSecurityNormalizer();
        $normalizers[] = new Oauth2ApplicationSecurityNormalizer();
        $normalizers[] = new Oauth2AccessCodeSecurityNormalizer();
        $normalizers[] = new JsonReferenceNormalizer();

        return $normalizers;
    }
}
