<?php

namespace Biig\Happii\Serialization\Json;

use Biig\Happii\Response\Created;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreatedNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = array())
    {
        $res = [];
        $resource = $object->getResourceId();

        if ($resource !== null) {
            $res['resource'] = $resource;
        }
        $res['id'] = $object->getId();

        return $res;
    }

    public function supportsNormalization($data, $format = null)
    {
        return is_object($data) && $data instanceof Created;
    }
}
