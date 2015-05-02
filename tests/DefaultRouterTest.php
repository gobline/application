<?php

use Mendo\Translator\Translator;
use Mendo\Http\Request\StringHttpRequest;
use Mendo\Http\Request\Resolver\LanguageSubdirectoryResolver;
use Mendo\Mvc\Router\DefaultRouter;
use Mendo\Router\RequestMatcher;
use Mendo\Router\UrlMaker;
use Mendo\Router\RouterCollection;
use Mendo\Router\RouteData;

class DefaultRouterTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultRouter()
    {
        $routers = new RouterCollection();

        $routers->add(new DefaultRouter(['index', 'users']));

        $requestMatcher = new RequestMatcher($routers);
        $urlMaker = new UrlMaker($routers);

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/profile?foo='));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('default', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'index', '_controller' => 'profile', '_action' => 'index'], $routeData->getParams());
        $this->assertSame('/profile', $urlMaker->makeUrl(new RouteData('default', ['_controller' => 'profile', '_action' => 'index']), 'en'));

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/profile/edit/foo/bar/corge/grault'));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('default', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'index', '_controller' => 'profile', '_action' => 'edit', 'foo' => 'bar', 'corge' => 'grault'], $routeData->getParams());
        $this->assertSame('/profile/edit/foo/bar/corge/grault', $urlMaker->makeUrl(new RouteData('default', ['_controller' => 'profile', '_action' => 'edit', 'foo' => 'bar', 'corge' => 'grault']), 'en'));

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/users/profile'));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('default', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'users', '_controller' => 'profile', '_action' => 'index'], $routeData->getParams());
        $this->assertSame('/users/profile', $urlMaker->makeUrl(new RouteData('default', ['_module' => 'users', '_controller' => 'profile', '_action' => 'index']), 'en'));

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/users/profile/edit/foo/bar/corge/grault'));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('default', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'users', '_controller' => 'profile', '_action' => 'edit', 'foo' => 'bar', 'corge' => 'grault'], $routeData->getParams());
        $this->assertSame('/users/profile/edit/foo/bar/corge/grault', $urlMaker->makeUrl(new RouteData('default', ['_module' => 'users', '_controller' => 'profile', '_action' => 'edit', 'foo' => 'bar', 'corge' => 'grault']), 'en'));
    }

    public function testDefaultI18nRouter()
    {
        $translator = new Translator();
        $translator->addTranslationArray([
            'users' => 'membres',
            'profile' => 'profil',
            'edit' => 'modifier',
            'foo' => 'toto',
            'bar' => 'titi',
            'corge' => 'machin',
            'grault' => 'truc',
        ], 'fr');

        $routers = new RouterCollection();

        $router = new DefaultRouter(['index', 'users']);
        $router->setTranslator($translator);
        $routers->add($router);

        $requestMatcher = new RequestMatcher($routers);
        $urlMaker = new UrlMaker($routers);

        $httpRequest = new StringHttpRequest('http://example.com/profile/edit');
        (new LanguageSubdirectoryResolver(['fr', 'en'], 'en'))->resolve($httpRequest);
        $routeData = $requestMatcher->match($httpRequest);

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('default', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'index', '_controller' => 'profile', '_action' => 'edit'], $routeData->getParams());
        $this->assertSame('/profile/edit/foo/bar/corge/grault', $urlMaker->makeUrl(new RouteData('default', ['_controller' => 'profile', '_action' => 'edit', 'foo' => 'bar', 'corge' => 'grault']), 'en'));

        $httpRequest = new StringHttpRequest('http://example.com/fr/profil/modifier/toto/titi/machin/truc');
        (new LanguageSubdirectoryResolver(['fr', 'en'], 'en'))->resolve($httpRequest);
        $routeData = $requestMatcher->match($httpRequest);

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('default', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'index', '_controller' => 'profile', '_action' => 'edit', 'foo' => 'bar', 'corge' => 'grault'], $routeData->getParams());
        $this->assertSame('/profil/modifier/toto/titi/machin/truc', $urlMaker->makeUrl(new RouteData('default', ['_controller' => 'profile', '_action' => 'edit', 'foo' => 'bar', 'corge' => 'grault']), 'fr'));

        $httpRequest = new StringHttpRequest('http://example.com/fr/membres/profil/modifier/toto/titi/machin/truc');
        (new LanguageSubdirectoryResolver(['fr', 'en'], 'en'))->resolve($httpRequest);
        $routeData = $requestMatcher->match($httpRequest);

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('default', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'users', '_controller' => 'profile', '_action' => 'edit', 'foo' => 'bar', 'corge' => 'grault'], $routeData->getParams());
        $this->assertSame('/membres/profil/modifier/toto/titi/machin/truc', $urlMaker->makeUrl(new RouteData('default', ['_module' => 'users', '_controller' => 'profile', '_action' => 'edit', 'foo' => 'bar', 'corge' => 'grault']), 'fr'));
    }
}
