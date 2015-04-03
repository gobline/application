<?php

use Mendo\Translator\Translator;
use Mendo\Http\Request\StringHttpRequest;
use Mendo\Http\Request\Resolver\LanguageSubdirectoryResolver;
use Mendo\Mvc\Router\TemplateRouter;
use Mendo\Router\RequestMatcher;
use Mendo\Router\UrlMaker;
use Mendo\Router\RouterCollection;
use Mendo\Router\RouteData;

class TemplateRouterTest extends PHPUnit_Framework_TestCase
{
    public function testTemplateRouter()
    {
        $routers = new RouterCollection();

        $router = new TemplateRouter();
        $router->setDefaultCatchallModule('index');

        $routes = [
            '/tutorial/mvc/model',
            '/tutorial/mvc/view',
            '/tutorial/mvc/controller',
        ];
        $routes = array_combine($routes, $routes);
        $router->setTemplates('tutorial', $routes);

        $routes = [
            '/docs/getting-started/introduction',
            '/docs/getting-started/skeleton',
            '/docs/getting-started/modules',
        ];
        $routes = array_combine($routes, $routes);
        $routes['/docs/getting-started/routers'] = '/docs/getting-started/routers-mvc';
        $router->setTemplates('docs', $routes);

        $routers->add($router);

        $requestMatcher = new RequestMatcher($routers);
        $urlMaker = new UrlMaker($routers);

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/tutorial/mvc/view'));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('template', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'tutorial', '_template' => 'tutorial/mvc/view'], $routeData->getParams());
        $this->assertSame('/tutorial/mvc/view', $urlMaker->makeUrl(new RouteData('template', ['_template' => 'tutorial/mvc/view']), 'en'));

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/docs/getting-started/skeleton'));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('template', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'docs', '_template' => 'docs/getting-started/skeleton'], $routeData->getParams());
        $this->assertSame('/docs/getting-started/skeleton', $urlMaker->makeUrl(new RouteData('template', ['_template' => 'docs/getting-started/skeleton']), 'en'));

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/docs/getting-started/routers-mvc'));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('template', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'docs', '_template' => 'docs/getting-started/routers'], $routeData->getParams());
        $this->assertSame('/docs/getting-started/routers-mvc', $urlMaker->makeUrl(new RouteData('template', ['_template' => 'docs/getting-started/routers']), 'en'));
        $this->assertSame('/docs/getting-started/routers-mvc', $urlMaker->makeUrl(new RouteData('template', ['_template' => 'docs/getting-started/routers', '_module' => 'docs']), 'en'));

        $routeData = $requestMatcher->match(new StringHttpRequest('http://example.com/foo/bar'));

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('template', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'index', '_template' => 'foo/bar'], $routeData->getParams());
        $this->assertSame('/foo/bar', $urlMaker->makeUrl(new RouteData('template', ['_template' => 'foo/bar']), 'en'));
    }

    public function testTemplateI18nRouter()
    {
        $router = new TemplateRouter();

        $translator = new Translator();
        $router->setTranslator($translator);
        $router->setDefaultCatchallModule('index');

        $routes = [
            '/tutorial/mvc/model',
            '/tutorial/mvc/view',
            '/tutorial/mvc/controller',
        ];
        $routes = array_combine($routes, $routes);
        $router->setTemplates('tutorial', $routes);

        $translator->addTranslationArray([
            '/tutorial/mvc/model' => '/tutoriel/mvc/modele',
            '/tutorial/mvc/view' => '/tutoriel/mvc/vue',
            '/tutorial/mvc/controller' => '/tutoriel/mvc/controleur',
        ], 'fr');

        $routes = [
            '/docs/getting-started/introduction',
            '/docs/getting-started/skeleton',
            '/docs/getting-started/modules',
        ];
        $routes = array_combine($routes, $routes);
        $routes['/docs/getting-started/routers'] = '/docs/getting-started/routers-mvc';
        $router->setTemplates('docs', $routes);

        $translator->addTranslationArray([
            '/docs/getting-started/introduction' => '/docs/commencer/introduction',
            '/docs/getting-started/skeleton' => '/docs/commencer/skeleton',
            '/docs/getting-started/modules' => '/docs/commencer/modules',
            '/docs/getting-started/routers' => '/docs/commencer/routers-mvc',
        ], 'fr');

        $routers = new RouterCollection();
        $routers->add($router);

        $requestMatcher = new RequestMatcher($routers);
        $urlMaker = new UrlMaker($routers);

        // english, no translations

        $httpRequest = new StringHttpRequest('http://example.com/tutorial/mvc/view');
        (new LanguageSubdirectoryResolver(['fr', 'en'], 'en'))->resolve($httpRequest);
        $routeData = $requestMatcher->match($httpRequest);

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('template', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'tutorial', '_template' => 'tutorial/mvc/view'], $routeData->getParams());
        $this->assertSame('/tutorial/mvc/view', $urlMaker->makeUrl(new RouteData('template', ['_template' => 'tutorial/mvc/view']), 'en'));

        $httpRequest = new StringHttpRequest('http://example.com/docs/getting-started/routers-mvc');
        (new LanguageSubdirectoryResolver(['fr', 'en'], 'en'))->resolve($httpRequest);
        $routeData = $requestMatcher->match($httpRequest);

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('template', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'docs', '_template' => 'docs/getting-started/routers'], $routeData->getParams());
        $this->assertSame('/docs/getting-started/routers-mvc', $urlMaker->makeUrl(new RouteData('template', ['_template' => 'docs/getting-started/routers']), 'en'));
        $this->assertSame('/docs/getting-started/routers-mvc', $urlMaker->makeUrl(new RouteData('template', ['_template' => 'docs/getting-started/routers', '_module' => 'docs']), 'en'));

        // french

        $httpRequest = new StringHttpRequest('http://example.com/fr/tutoriel/mvc/vue');
        (new LanguageSubdirectoryResolver(['fr', 'en'], 'en'))->resolve($httpRequest);
        $routeData = $requestMatcher->match($httpRequest);

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('template', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'tutorial', '_template' => 'tutorial/mvc/view'], $routeData->getParams());
        $this->assertSame('/tutoriel/mvc/vue', $urlMaker->makeUrl(new RouteData('template', ['_template' => 'tutorial/mvc/view']), 'fr'));

        $httpRequest = new StringHttpRequest('http://example.com/fr/docs/commencer/routers-mvc');
        (new LanguageSubdirectoryResolver(['fr', 'en'], 'en'))->resolve($httpRequest);
        $routeData = $requestMatcher->match($httpRequest);

        $this->assertInstanceOf('Mendo\Router\RouteData', $routeData);
        $this->assertSame('template', $routeData->getRouteName());
        $this->assertEquals(['_module' => 'docs', '_template' => 'docs/getting-started/routers'], $routeData->getParams());
        $this->assertSame('/docs/commencer/routers-mvc', $urlMaker->makeUrl(new RouteData('template', ['_template' => 'docs/getting-started/routers']), 'fr'));
        $this->assertSame('/docs/commencer/routers-mvc', $urlMaker->makeUrl(new RouteData('template', ['_template' => 'docs/getting-started/routers', '_module' => 'docs']), 'fr'));
    }
}
