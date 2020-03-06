<?php

namespace Biig\Melodiia\Crud\Controller;

use Biig\Melodiia\Crud\CrudControllerInterface;
use Biig\Melodiia\Crud\Event\CrudEvent;
use Biig\Melodiia\Crud\Event\CustomResponseEvent;
use Biig\Melodiia\Crud\Persistence\DataStoreInterface;
use Biig\Melodiia\Response\Ok;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class Delete extends BaseCrudController implements CrudControllerInterface
{
    use CrudControllerTrait;

    public const EVENT_PRE_DELETE = 'melodiia.crud.pre_delete';
    public const EVENT_POST_DELETE = 'melodiia.crud.post_delete';

    /** @var DataStoreInterface */
    private $dataStore;

    /** @var AuthorizationCheckerInterface */
    private $checker;

    public function __construct(DataStoreInterface $dataStore, AuthorizationCheckerInterface $checker, EventDispatcherInterface $dispatcher)
    {
        parent::__construct($dispatcher);
        $this->dataStore = $dataStore;
        $this->checker = $checker;
    }

    public function __invoke(Request $request, $id)
    {
        $modelClass = $request->attributes->get(self::MODEL_ATTRIBUTE);
        $securityCheck = $request->attributes->get(self::SECURITY_CHECK, null);

        $this->assertModelClassInvalid($modelClass);

        $data = $this->dataStore->find($modelClass, $id);

        if (null === $data) {
            throw new NotFoundHttpException(\sprintf('resource item "%s" with id "%s" can not be found', $modelClass, $id));
        }

        if ($securityCheck && !$this->checker->isGranted($securityCheck, $data)) {
            throw new AccessDeniedException(\sprintf('You can\'t perform a delete operation of the resource item "%s" with id "%s"',
                $modelClass,
                $id
            ));
        }

        $this->dispatch(new CrudEvent($data), self::EVENT_PRE_DELETE);
        $this->dataStore->remove($data);
        $this->dispatch($event = new CustomResponseEvent($data),self::EVENT_POST_DELETE);

        if ($event->hasCustomResponse()) {
            return $event->getResponse();
        }

        return new Ok('Deletion ok');
    }
}
