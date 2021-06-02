<?php

declare(strict_types=1);

/*
 * This file is part of the Runroom package.
 *
 * (c) Runroom <runroom@runroom.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Runroom\SortableBehaviorBundle\Tests\Unit;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Runroom\SortableBehaviorBundle\Controller\SortableAdminController;
use Runroom\SortableBehaviorBundle\Service\PositionHandlerInterface;
use Runroom\SortableBehaviorBundle\Tests\App\Entity\ChildSortableEntity;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Admin\BreadcrumbsBuilderInterface;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\AdminBundle\Templating\TemplateRegistry;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Translation\Translator;

class SortableAdminControllerTest extends TestCase
{
    private PropertyAccessor $propertyAccessor;

    /** @var MockObject&PositionHandlerInterface */
    private $positionHandler;

    private Container $container;

    /** @var MockObject&AdminInterface<object> */
    private $admin;

    private Request $request;
    private SortableAdminController $controller;

    protected function setUp(): void
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->positionHandler = $this->createMock(PositionHandlerInterface::class);
        $this->container = new Container();
        $this->admin = $this->createMock(AdminInterface::class);
        $this->request = new Request();

        $this->configureCRUDController();
        $this->configureRequest();
        $this->configureContainer();

        $this->controller = new SortableAdminController(
            $this->propertyAccessor,
            $this->positionHandler
        );
        $this->controller->setContainer($this->container);
        $this->controller->configureAdmin($this->request);
    }

    /** @test */
    public function itRedirectsWhenMissingPermissions(): void
    {
        $this->admin->method('isGranted')->with('EDIT')->willReturn(false);
        $this->admin->method('generateUrl')->with('list', ['filter' => []])->willReturn('https://localhost');
        $this->admin->method('getFilterParameters')->willReturn([]);
        $this->admin->method('getTranslationDomain')->willReturn('domain');

        $response = $this->controller->moveAction('up');

        self::assertInstanceOf(RedirectResponse::class, $response);
    }

    /** @test */
    public function itMovesPositions(): void
    {
        $entity = new ChildSortableEntity();

        $this->admin->method('isGranted')->with('EDIT')->willReturn(true);
        $this->admin->method('getSubject')->willReturn($entity);
        $this->admin->method('generateUrl')->with('list', ['filter' => []])->willReturn('https://localhost');
        $this->admin->method('getFilterParameters')->willReturn([]);
        $this->admin->expects(self::once())->method('update')->with($entity);
        $this->admin->method('getTranslationDomain')->willReturn('domain');
        $this->positionHandler->method('getLastPosition')->with($entity)->willReturn(2);
        $this->positionHandler->method('getPosition')->with($entity, 'up', 2)->willReturn(1);
        $this->positionHandler->method('getPositionFieldByEntity')->with($entity)->willReturn('position');

        $response = $this->controller->moveAction('up');

        self::assertInstanceOf(RedirectResponse::class, $response);
    }

    /** @test */
    public function itMovesPositionsWithAjax(): void
    {
        $entity = new ChildSortableEntity();

        $this->request->headers->set('X-Requested-With', 'XMLHttpRequest');
        $this->admin->method('isGranted')->with('EDIT')->willReturn(true);
        $this->admin->method('getSubject')->willReturn($entity);
        $this->admin->expects(self::once())->method('update')->with($entity);
        $this->admin->method('getNormalizedIdentifier')->with($entity)->willReturn('identifier');
        $this->positionHandler->method('getLastPosition')->with($entity)->willReturn(2);
        $this->positionHandler->method('getPosition')->with($entity, 'up', 2)->willReturn(1);
        $this->positionHandler->method('getPositionFieldByEntity')->with($entity)->willReturn('position');

        $response = $this->controller->moveAction('up');

        self::assertInstanceOf(JsonResponse::class, $response);
    }

    private function configureCRUDController(): void
    {
        /* @phpstan-ignore-next-line */
        if (method_exists(AdminInterface::class, 'hasTemplateRegistry')) {
            $this->admin->method('hasTemplateRegistry')->willReturn(true);
        }
        $this->admin->method('isChild')->willReturn(false);
        $this->admin->method('setRequest')->with($this->request);
        $this->admin->method('getCode')->willReturn('admin_code');
    }

    private function configureRequest(): void
    {
        $this->request->query->set('_sonata_admin', 'admin_code');
    }

    private function configureContainer(): void
    {
        $translator = new Translator('en');
        $breadcrumbsBuilder = $this->createStub(BreadcrumbsBuilderInterface::class);

        $pool = new Pool($this->container, [
            'admin.code' => 'admin_code',
        ]);
        $session = $this->createStub(Session::class);
        $flashBag = new FlashBag();

        $session->method('getFlashBag')->willReturn($flashBag);

        $requestStack = new RequestStack();
        $requestStack->push($this->request);

        $this->request->setSession($session);

        $this->container->set('admin_code', $this->admin);
        $this->container->set('request_stack', $requestStack);
        $this->container->set('translator', $translator);
        $this->container->set('session', $session);
        $this->container->set('admin_code.template_registry', new TemplateRegistry());
        $this->container->set('sonata.admin.pool', $pool);
        $this->container->set('sonata.admin.pool.do-not-use', $pool);
        $this->container->set('sonata.admin.breadcrumbs_builder', $breadcrumbsBuilder);
        $this->container->set('sonata.admin.breadcrumbs_builder.do-not-use', $breadcrumbsBuilder);
    }
}
