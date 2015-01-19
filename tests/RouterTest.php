<?php

use Mendo\Translator\Translator;
use Mendo\Http\Request\StringHttpRequest;
use Mendo\Http\Request\Resolver\LanguageSubdirectoryResolver;
use Mendo\Mvc\Request\MvcRequest;
use Mendo\Mvc\Router\DefaultRouter;
use Mendo\Mvc\Router\I18n\DefaultRouter as DefaultI18nRouter;
use Mendo\Mvc\Router\LiteralRouter;
use Mendo\Mvc\Router\I18n\LiteralRouter as LiteralI18nRouter;
use Mendo\Mvc\Router\PlaceholderRouter;
use Mendo\Mvc\Router\I18n\PlaceholderRouter as PlaceholderI18nRouter;

class RouterTest extends PHPUnit_Framework_TestCase
{
    public function testDefaultRouter() 
    {
        $url = 'http://example.com/member/profile/edit';

        $httpRequest = new StringHttpRequest($url);

        $listModules = ['main', 'member'];
        $router = new DefaultRouter($listModules);

        $mvcRequest = $router->match($httpRequest);

        $this->assertInstanceOf('Mendo\Mvc\Request\MvcRequest', $mvcRequest);
        $this->assertSame('member', $mvcRequest->getModule());
        $this->assertSame('profile', $mvcRequest->getController());
        $this->assertSame('edit', $mvcRequest->getAction());
        $this->assertSame('/member/profile/edit/hello/world', 
            $router->makeUrl(new MvcRequest('default', 'member', 'profile', 'edit', ['hello' => 'world'])));
        $this->assertSame('/member', 
            $router->makeUrl(new MvcRequest('default', 'member', 'index', 'index')));
    }

    public function testDefaultI18nRouter() 
    {
        $url = 'http://example.com/fr/membre/profil/modifier';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $listModules = ['main', 'member'];
        $router = new DefaultI18nRouter($listModules, ['member']);
        $translator = new Translator();
        $translator->addTranslationFile(__DIR__ . '/resources/test-translator-fr.php', 'fr');
        $translator->addTranslationFile(__DIR__ . '/resources/test-translator-nl.php', 'nl');
        $router->setTranslator($translator);

        $mvcRequest = $router->match($httpRequest);

        $this->assertInstanceOf('Mendo\Mvc\Request\MvcRequest', $mvcRequest);
        $this->assertSame('member', $mvcRequest->getModule());
        $this->assertSame('profile', $mvcRequest->getController());
        $this->assertSame('edit', $mvcRequest->getAction());
        $this->assertSame('/gebruiker/profiel/wijzigen/hallo/world', 
            $router->makeUrl(new MvcRequest('translator', 'member', 'profile', 'edit', ['hello' => 'world']), 'nl'));
    }

    public function testLiteralRouter()
    {
        $url = 'http://example.com/profile/edit';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $router = new LiteralRouter(
            'edit-profile', 
            '/profile/edit', 
            [
                'module' => 'member',
                'controller' => 'profile',
                'action' => 'edit',
            ]
        );

        $mvcRequest = $router->match($httpRequest);

        $this->assertInstanceOf('Mendo\Mvc\Request\MvcRequest', $mvcRequest);
        $this->assertSame('member', $mvcRequest->getModule());
        $this->assertSame('profile', $mvcRequest->getController());
        $this->assertSame('edit', $mvcRequest->getAction());
        $this->assertSame('/profile/edit', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit'), 'en'));
    }

    public function testLiteralI18nRouter()
    {
        $url = 'http://example.com/fr/profil/modifier';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $router = new LiteralI18nRouter(
            'edit-profile', 
            '/profile/edit', 
            [
                'module' => 'member',
                'controller' => 'profile',
                'action' => 'edit',
            ]
        );
        $translator = new Translator();
        $translator->addTranslationFile(__DIR__ . '/resources/test-translator-fr.php', 'fr');
        $translator->addTranslationFile(__DIR__ . '/resources/test-translator-nl.php', 'nl');
        $router->setTranslator($translator);

        $mvcRequest = $router->match($httpRequest);

        $this->assertInstanceOf('Mendo\Mvc\Request\MvcRequest', $mvcRequest);
        $this->assertSame('member', $mvcRequest->getModule());
        $this->assertSame('profile', $mvcRequest->getController());
        $this->assertSame('edit', $mvcRequest->getAction());
        $this->assertSame('/profile/edit', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit'), 'en'));
        $this->assertSame('/profiel/wijzigen', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit'), 'nl'));
    }

    public function testPlaceholderRouter()
    {
        $router = new PlaceholderRouter(
            'profile', 
            '/profile/:action/:user[/bacon/:foo[/:bar/:baz]]', 
            [
                'module' => 'member',
                'controller' => 'profile',
                'foo' => 'qux',
                'bar' => 'corge',
            ],
            [
                'action' => '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/',
                'foo' => '/^[a-zA-Z]+$/',
                'bar' => '/^[a-zA-Z]+$/',
            ]
        );
        $this->assertSame('/profile/edit/grault', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit', ['user' => 'grault']), 'en')); // todo, better pass an array than a request with null values?
        $this->assertSame('/profile/edit/grault', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit', ['user' => 'grault', 'foo' => 'qux']), 'en'));
        $this->assertSame('/profile/edit/grault/bacon/quux', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit', ['user' => 'grault', 'foo' => 'quux']), 'en'));
        // throws exception as expected: value for :bar placeholder but not for :baz
        //$this->assertSame('/profile/edit/grault/bacon/qux/garply', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit', ['user' => 'grault', 'bar' => 'garply']), 'en'));
        $this->assertSame('/profile/edit/grault/bacon/qux/garply/thud', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit', ['user' => 'grault', 'bar' => 'garply', 'baz' => 'thud']), 'en'));
        $this->assertSame('/profile/edit/grault/bacon/qux/corge/thud', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit', ['user' => 'grault', 'baz' => 'thud']), 'en'));
        $this->assertSame('/profile/edit/grault/bacon/qux/corge/thud', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit', ['user' => 'grault', 'foo' => 'qux', 'bar' => 'corge', 'baz' => 'thud']), 'en'));
        $this->assertSame('/profile/edit/grault/bacon/quux/garply/thud', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit', ['user' => 'grault', 'foo' => 'quux', 'bar' => 'garply', 'baz' => 'thud']), 'en'));

        // required :user segment missing
        $url = 'http://example.com/profile/edit';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $this->assertFalse($router->match($httpRequest));

        $url = 'http://example.com/profile/edit/matthewbe';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $mvcRequest = $router->match($httpRequest);
        $this->assertInstanceOf('Mendo\Mvc\Request\MvcRequest', $mvcRequest);
        $this->assertSame('member', $mvcRequest->getModule());
        $this->assertSame('profile', $mvcRequest->getController());
        $this->assertSame('edit', $mvcRequest->getAction());
        $this->assertSame('matthewbe', $mvcRequest->getParam('user'));
        $this->assertSame('qux', $mvcRequest->getParam('foo'));
        $this->assertSame('corge', $mvcRequest->getParam('bar'));

        // optional /bacon/:foo part is missing :foo segment
        $url = 'http://example.com/profile/edit/matthewbe/bacon';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $this->assertFalse($router->match($httpRequest));


        $url = 'http://example.com/profile/edit/matthewbe/bacon/hello';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $mvcRequest = $router->match($httpRequest);
        $this->assertInstanceOf('Mendo\Mvc\Request\MvcRequest', $mvcRequest);
        $this->assertSame('member', $mvcRequest->getModule());
        $this->assertSame('profile', $mvcRequest->getController());
        $this->assertSame('edit', $mvcRequest->getAction());
        $this->assertSame('matthewbe', $mvcRequest->getParam('user'));
        $this->assertSame('hello', $mvcRequest->getParam('foo'));
        $this->assertSame('corge', $mvcRequest->getParam('bar'));


        $url = 'http://example.com/profile/edit/matthewbe/ham/hello';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $this->assertFalse($router->match($httpRequest));


        $url = 'http://example.com/profile/edit/matthewbe/bacon/hello/world/thud';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $mvcRequest = $router->match($httpRequest);
        $this->assertInstanceOf('Mendo\Mvc\Request\MvcRequest', $mvcRequest);
        $this->assertSame('member', $mvcRequest->getModule());
        $this->assertSame('profile', $mvcRequest->getController());
        $this->assertSame('edit', $mvcRequest->getAction());
        $this->assertSame('matthewbe', $mvcRequest->getParam('user'));
        $this->assertSame('hello', $mvcRequest->getParam('foo'));
        $this->assertSame('world', $mvcRequest->getParam('bar'));
        $this->assertSame('thud', $mvcRequest->getParam('baz'));

        // optional /:bar/:baz part is missing :baz segment
        $url = 'http://example.com/profile/edit/matthewbe/bacon/hello/world';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $this->assertFalse($router->match($httpRequest));

        // plugh couldn't be matched by any route segment
        $url = 'http://example.com/profile/edit/matthewbe/bacon/hello/world/thud/plugh';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $this->assertFalse($router->match($httpRequest));

        // test constraints
        $url = 'http://example.com/profile/edit/matthewbe/bacon/123';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $this->assertFalse($router->match($httpRequest));
    }

    public function testPlaceholderI18nRouter()
    {
        $router = new PlaceholderI18nRouter(
            'profile', 
            '/profile/:action/:user[/bacon/:foo[/:bar/:baz]]', 
            [
                'module' => 'member',
                'controller' => 'profile',
                'foo' => 'qux',
                'bar' => 'corge',
            ],
            [
                'action' => '/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/',
                'foo' => '/^[a-zA-Z]+$/',
                'bar' => '/^[a-zA-Z]+$/',
            ]
        );
        $translator = new Translator();
        $translator->addTranslationFile(__DIR__ . '/resources/test-translator-fr.php', 'fr');
        $translator->addTranslationFile(__DIR__ . '/resources/test-translator-nl.php', 'nl');
        $router->setTranslator($translator);

        $this->assertSame('/profil/modifier/grault', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit', ['user' => 'grault']), 'fr'));
        $this->assertSame('/profil/modifier/grault/lard/quux', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit', ['user' => 'grault', 'foo' => 'quux']), 'fr'));
        $this->assertSame('/profil/modifier/grault/lard/qux/garply/thud', $router->makeUrl(new MvcRequest('edit-profile', 'member', 'profile', 'edit', ['user' => 'grault', 'bar' => 'garply', 'baz' => 'thud']), 'fr'));


        $url = 'http://example.com/fr/profil/modifier/matthewbe';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $mvcRequest = $router->match($httpRequest);
        $this->assertInstanceOf('Mendo\Mvc\Request\MvcRequest', $mvcRequest);
        $this->assertSame('member', $mvcRequest->getModule());
        $this->assertSame('profile', $mvcRequest->getController());
        $this->assertSame('edit', $mvcRequest->getAction());
        $this->assertSame('matthewbe', $mvcRequest->getParam('user'));
        $this->assertSame('qux', $mvcRequest->getParam('foo'));
        $this->assertSame('corge', $mvcRequest->getParam('bar'));


        $url = 'http://example.com/fr/profil/modifier/matthewbe/lard/hello/world/thud';

        $httpRequest = new StringHttpRequest($url);
        (new LanguageSubdirectoryResolver(['fr', 'nl', 'en'], 'en'))->resolve($httpRequest);

        $mvcRequest = $router->match($httpRequest);
        $this->assertInstanceOf('Mendo\Mvc\Request\MvcRequest', $mvcRequest);
        $this->assertSame('member', $mvcRequest->getModule());
        $this->assertSame('profile', $mvcRequest->getController());
        $this->assertSame('edit', $mvcRequest->getAction());
        $this->assertSame('matthewbe', $mvcRequest->getParam('user'));
        $this->assertSame('hello', $mvcRequest->getParam('foo'));
        $this->assertSame('world', $mvcRequest->getParam('bar'));
        $this->assertSame('thud', $mvcRequest->getParam('baz'));
    }
}
