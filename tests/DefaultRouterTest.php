<?php

use Mendo\Translator\Translator;
use Mendo\Http\Request\StringHttpRequest;
use Mendo\Http\Request\Resolver\LanguageSubdirectoryResolver;
use Mendo\Mvc\Router\DefaultRouter;
use Mendo\Router\PlaceholderRouter;
use Mendo\Router\I18n\PlaceholderRouter as PlaceholderI18nRouter;
use Mendo\Router\RequestMatcher;
use Mendo\Router\UrlMaker;
use Mendo\Router\RouterCollection;
use Mendo\Router\RouteData;

class DefaultRouterTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultRouterMatch()
    {
        $routers = new RouterCollection();

        $routers->add(
            new DefaultRouter(
                'default',
                '(/)',
                [
                    'module' => 'index',
                    'controller' => 'index',
                    'action' => 'index',
                ]
            ));

        $routers->add(
            new DefaultRouter(
                'users',
                '/users',
                [
                    'module' => 'users',
                    'controller' => 'index',
                    'action' => 'index',
                ]
            ));

        $requestMatcher = new RequestMatcher($routers);
        $urlMaker = new UrlMaker($routers);

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/profile'));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('default', $routeData->getRouteName());
        $this->assertEquals(['module' => 'index', 'controller' => 'profile', 'action' => 'index'], $routeData->getParams());
        $this->assertSame('/profile', $urlMaker->makeUrl(new RouteData('default', ['controller' => 'profile', 'action' => 'index']), 'en'));

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/profile/edit/foo/bar/corge/grault'));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('default', $routeData->getRouteName());
        $this->assertEquals(['module' => 'index', 'controller' => 'profile', 'action' => 'edit', 'foo' => 'bar', 'corge' => 'grault'], $routeData->getParams());
        $this->assertSame('/profile/edit/foo/bar/corge/grault', $urlMaker->makeUrl(new RouteData('default', ['controller' => 'profile', 'action' => 'edit', 'foo' => 'bar', 'corge' => 'grault']), 'en'));

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/users/profile'));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('users', $routeData->getRouteName());
        $this->assertEquals(['module' => 'users', 'controller' => 'profile', 'action' => 'index'], $routeData->getParams());
        $this->assertSame('/users/profile', $urlMaker->makeUrl(new RouteData('users', ['controller' => 'profile', 'action' => 'index']), 'en'));

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/users/profile/edit/foo/bar/corge/grault'));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('users', $routeData->getRouteName());
        $this->assertEquals(['module' => 'users', 'controller' => 'profile', 'action' => 'edit', 'foo' => 'bar', 'corge' => 'grault'], $routeData->getParams());
        $this->assertSame('/users/profile/edit/foo/bar/corge/grault', $urlMaker->makeUrl(new RouteData('users', ['controller' => 'profile', 'action' => 'edit', 'foo' => 'bar', 'corge' => 'grault']), 'en'));
    }

    public function testDefaultI18nRouterMatch()
    {
        $translator = new Translator();
        $translator->addTranslationArray([
            '/users(/:controller(/)(/:action(/)(/:params+)))' => '/membres(/:controller(/)(/:action(/)(/:params+)))',
            'profile' => 'profil',
            'edit' => 'modifier',
            'foo' => 'toto',
            'bar' => 'titi',
            'corge' => 'machin',
            'grault' => 'truc',
        ], 'fr');

        $routers = new RouterCollection();

        $routers->add(
            new DefaultRouter(
                'default',
                '(/)',
                [
                    'module' => 'index',
                    'controller' => 'index',
                    'action' => 'index',
                ],
                $translator
            ));

        $routers->add(
            new DefaultRouter(
                'users',
                '/users',
                [
                    'module' => 'users',
                    'controller' => 'index',
                    'action' => 'index',
                ],
                $translator
            ));

        $requestMatcher = new RequestMatcher($routers);
        $urlMaker = new UrlMaker($routers);

        $httpRequest = new StringHttpRequest('http://example.com/profile/edit');
        (new LanguageSubdirectoryResolver(['fr', 'en'], 'en'))->resolve($httpRequest);
        $routeData = $requestMatcher->match($httpRequest);

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('default', $routeData->getRouteName());
        $this->assertEquals(['module' => 'index', 'controller' => 'profile', 'action' => 'edit'], $routeData->getParams());
        $this->assertSame('/profile/edit/foo/bar/corge/grault', $urlMaker->makeUrl(new RouteData('default', ['controller' => 'profile', 'action' => 'edit', 'foo' => 'bar', 'corge' => 'grault']), 'en'));

        $httpRequest = new StringHttpRequest('http://example.com/fr/profil/modifier/toto/titi/machin/truc');
        (new LanguageSubdirectoryResolver(['fr', 'en'], 'en'))->resolve($httpRequest);
        $routeData = $requestMatcher->match($httpRequest);

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('default', $routeData->getRouteName());
        $this->assertEquals(['module' => 'index', 'controller' => 'profile', 'action' => 'edit', 'foo' => 'bar', 'corge' => 'grault'], $routeData->getParams());
        $this->assertSame('/profil/modifier/toto/titi/machin/truc', $urlMaker->makeUrl(new RouteData('default', ['controller' => 'profile', 'action' => 'edit', 'foo' => 'bar', 'corge' => 'grault']), 'fr'));

        $httpRequest = new StringHttpRequest('http://example.com/fr/membres/profil/modifier/toto/titi/machin/truc');
        (new LanguageSubdirectoryResolver(['fr', 'en'], 'en'))->resolve($httpRequest);
        $routeData = $requestMatcher->match($httpRequest);

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('users', $routeData->getRouteName());
        $this->assertEquals(['module' => 'users', 'controller' => 'profile', 'action' => 'edit', 'foo' => 'bar', 'corge' => 'grault'], $routeData->getParams());
        $this->assertSame('/membres/profil/modifier/toto/titi/machin/truc', $urlMaker->makeUrl(new RouteData('users', ['controller' => 'profile', 'action' => 'edit', 'foo' => 'bar', 'corge' => 'grault']), 'fr'));
    }
}
