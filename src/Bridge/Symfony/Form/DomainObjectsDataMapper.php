<?php

namespace Biig\Melodiia\Bridge\Symfony\Form;

use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper;
use Symfony\Component\Form\FormInterface;

/**
 * Add support for objects that require constructor instantiation.
 */
class DomainObjectsDataMapper extends PropertyPathMapper implements DomainObjectDataMapperInterface
{
    public function mapFormsToData($forms, &$data)
    {
        $data = $this->createObject($forms, !empty($data) && is_object($data) ? get_class($data) : null);
        parent::mapFormsToData($forms, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function createObject(iterable $form, string $dataClass = null, array $additionalData = [])
    {
        if (null === $dataClass && $form instanceof FormInterface) {
            $dataClass = $form->getConfig()->getOption('data_class');
        }

        $form = iterator_to_array($form);

        if (null === $dataClass || !class_exists($dataClass)) {
            return null;
        }

        $ref = new \ReflectionClass($dataClass);
        $constructorParameters = $ref->getConstructor()->getParameters();

        // Case of anemic object, we have nothing to do here.
        if (count($constructorParameters) < 1) {
            return new $dataClass();
        }

        $constructorData = [];
        foreach ($constructorParameters as $parameter) {
            if (isset($form[$parameter->getName()])) {
                if (null === $form[$parameter->getName()]->getData() && !$parameter->allowsNull() && !$parameter->isDefaultValueAvailable()) {
                    return null;
                }

                $data = $form[$parameter->getName()]->getData();
            } else if (isset($additionalData[$parameter->getName()])) {
                $data = $additionalData[$parameter->getName()];
            } else {
                $data = null;
            }

            if (empty($data) && $parameter->isDefaultValueAvailable()) {
                $data = $parameter->getDefaultValue();
            }

            $constructorData[] = $data;
        }

        return new $dataClass(...$constructorData);
    }
}
