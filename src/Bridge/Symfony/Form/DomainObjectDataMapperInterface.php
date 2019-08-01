<?php

namespace Biig\Melodiia\Bridge\Symfony\Form;

use Symfony\Component\Form\DataMapperInterface;

interface DomainObjectDataMapperInterface extends DataMapperInterface
{
    /**
     * @param iterable    $form
     * @param string|null $dataClass
     * @param mixed[]     $additionalData
     *
     * @return mixed
     */
    public function createObject(iterable $form, string $dataClass = null, array $additionalData = []);
}
