<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc;

use Pimple\Container;
use Mendo\Acl\Provider\Pimple\AclServiceProvider;
use Mendo\Auth\Provider\Pimple\AuthServiceProvider;
use Mendo\Filter\Provider\Pimple\FunnelFactoryServiceProvider;
use Mendo\Flash\Provider\Pimple\FlashServiceProvider;
use Mendo\Http\Provider\Pimple\HttpRequestServiceProvider;
use Mendo\Mediator\Provider\Pimple\EventDispatcherServiceProvider;
use Mendo\Session\Provider\Pimple\SessionServiceProvider;
use Mendo\Translator\Provider\Pimple\TranslatorServiceProvider;
use Mendo\Mvc\Provider\Pimple\ActionHelperContainerServiceProvider;
use Mendo\Mvc\Provider\Pimple\ErrorHandlerServiceProvider;
use Mendo\Mvc\Provider\Pimple\ErrorRedirectorServiceProvider;
use Mendo\Mvc\Provider\Pimple\HmvcRequestServiceProvider;
use Mendo\Mvc\Provider\Pimple\LayoutsServiceProvider;
use Mendo\Mvc\Provider\Pimple\ModulesServiceProvider;
use Mendo\Mvc\Provider\Pimple\MvcLocatorServiceProvider;
use Mendo\Mvc\Provider\Pimple\MvcRequestFactoryServiceProvider;
use Mendo\Mvc\Provider\Pimple\PluginsServiceProvider;
use Mendo\Mvc\Provider\Pimple\RequestAuthorizerServiceProvider;
use Mendo\Mvc\Provider\Pimple\RequestDispatcherServiceProvider;
use Mendo\Mvc\Provider\Pimple\RequestForwarderServiceProvider;
use Mendo\Mvc\Provider\Pimple\RequestRedirectorServiceProvider;
use Mendo\Mvc\Provider\Pimple\RoutersServiceProvider;
use Mendo\Mvc\Provider\Pimple\TemplateFileResolverServiceProvider;
use Mendo\Mvc\Provider\Pimple\UrlMakerServiceProvider;
use Mendo\Mvc\Provider\Pimple\ViewHelperContainerServiceProvider;
use Mendo\Mvc\Provider\Pimple\ViewRendererServiceProvider;
use Mendo\Mvc\Provider\Pimple\WhoopsServiceProvider;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class CompositionRoot extends Container
{
    public function __construct(array $values = [])
    {
        parent::__construct();

        $this->register(new HttpRequestServiceProvider('request.http'));
        $this->register(new AuthServiceProvider('auth'));
        $this->register(new AclServiceProvider('acl.routes'));
        $this['acl.routes.defaultResourceType'] = 'startsWith';
        $this->register(new EventDispatcherServiceProvider('eventDispatcher.view'));
        $this->register(new EventDispatcherServiceProvider('eventDispatcher.mvc'));
        $this->register(new SessionServiceProvider('session'));
        $this->register(new FlashServiceProvider('flash'));
        $this->register(new TranslatorServiceProvider('translator'));
        $this->register(new FunnelFactoryServiceProvider('filterFunnelFactory'));

        $this->register(new ActionHelperContainerServiceProvider());
        $this->register(new ErrorHandlerServiceProvider());
        $this->register(new ErrorRedirectorServiceProvider());
        $this->register(new HmvcRequestServiceProvider());
        $this->register(new LayoutsServiceProvider());
        $this->register(new ModulesServiceProvider());
        $this->register(new MvcLocatorServiceProvider());
        $this->register(new MvcRequestFactoryServiceProvider());
        $this->register(new PluginsServiceProvider());
        $this->register(new RequestAuthorizerServiceProvider());
        $this->register(new RequestDispatcherServiceProvider());
        $this->register(new RequestForwarderServiceProvider());
        $this->register(new RequestRedirectorServiceProvider());
        $this->register(new RoutersServiceProvider());
        $this->register(new TemplateFileResolverServiceProvider());
        $this->register(new UrlMakerServiceProvider());
        $this->register(new ViewHelperContainerServiceProvider());
        $this->register(new ViewRendererServiceProvider());
        $this->register(new WhoopsServiceProvider());

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
    }
}
