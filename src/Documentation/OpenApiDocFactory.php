<?php

namespace Biig\Melodiia\Documentation;

use Nekland\Tools\StringTools;
use OpenApi\Analysis;
use OpenApi\Annotations\Info;
use OpenApi\Annotations\OpenApi;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class OpenApiDocFactory.
 *
 * Feel free to decorate this class. Do not extend it.
 */
final class OpenApiDocFactory implements DocumentationFactoryInterface
{
    /** @var RequestStack */
    private $requestStack;

    /** @var array */
    private $config;

    public function __construct(RequestStack $requestStack, array $config)
    {
        $this->requestStack = $requestStack;
        $this->config = $config;
    }

    public function createOpenApiAnalysis(): Analysis
    {
        $config = [
            'info' => new Info([
                'title' => $this->config['title'],
                'version' => $this->config['version'],
                'description' => $this->config['description'],
            ]),
            'paths' => [],
            'servers' => [
                ['url' => $this->getApiUrl()],
            ],
        ];

        $analysis = new Analysis();

        $openApi = new OpenApi($config);
        $analysis->addAnnotation($openApi, null);

        return $analysis;
    }

    private function getApiUrl()
    {
        $path = $this->config['base_path'];
        if (!StringTools::startsWith($path, '/')) {
            $path = '/' . $path;
        }

        return $this->requestStack->getMasterRequest()->getSchemeAndHttpHost() . $path;
    }
}
