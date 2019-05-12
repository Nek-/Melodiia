<?php

namespace Biig\Melodiia\Crud;

use Biig\Melodiia\Exception\NoFormFilterCreatedException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class FilterCollection
{
    /** @var FilterInterface[] */
    private $filters;

    /** @var FormFactoryInterface */
    private $formFactory;

    /**
     * Cached form. This class is stateful.
     *
     * @var FormInterface
     */
    private $form;

    public function __construct(FormFactoryInterface $formFactory, array $filters)
    {
        $this->formFactory = $formFactory;
        foreach ($filters as $filter) {
            $this->add($filter);
        }
    }

    public function add(FilterInterface $filter): void
    {
        $this->filters[] = $filter;
    }

    public function filter(QueryBuilder $query): void
    {
        if (null === $this->form) {
            throw new NoFormFilterCreatedException('The filter form was not generated. You probably forgot to call `$collection->getForm()->handleRequest($request)`.');
        }

        foreach ($this->filters as $filter) {
            $filter->filter($query, $this->getForm());
        }
    }

    public function getForm(): FormInterface
    {
        if ($this->form) {
            return $this->form;
        }

        $builder = $this->formFactory->createNamedBuilder('', FormType::class, null, [
            'method' => 'GET',
            'csrf_protection' => false,
        ]);

        foreach ($this->filters as $filter) {
            $filter->buildForm($builder);
        }

        return $this->form = $builder->getForm();
    }
}