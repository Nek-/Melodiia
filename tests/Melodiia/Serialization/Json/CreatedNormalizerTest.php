<?php

namespace Biig\Melodiia\Test\Serialization\Json;

use Biig\Melodiia\Response\Created;
use Biig\Melodiia\Serialization\Json\CreatedNormalizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class CreatedNormalizerTest extends TestCase
{
    public function testItIsSymfonyNormalizer()
    {
        $normalizer = new CreatedNormalizer();
        $this->assertInstanceOf(NormalizerInterface::class, $normalizer);
    }

    public function testItSupportsOnlyResponse()
    {
        $normalizer = new CreatedNormalizer();

        $this->assertTrue($normalizer->supportsNormalization(new Created('foo')));
        $this->assertFalse($normalizer->supportsNormalization([]));
        $this->assertFalse($normalizer->supportsNormalization(new \stdClass()));
    }

    public function testItNormalizeCorrectly()
    {
        $normalizer = new CreatedNormalizer();

        $this->assertEquals(
            ['id' => 'foobar'],
            $normalizer->normalize(new Created('foobar'))
        );
        $this->assertEquals(
            ['resource' => 'User', 'id' => 'bazbar'],
            $normalizer->normalize(new Created('bazbar', 'User'))
        );
    }
}
