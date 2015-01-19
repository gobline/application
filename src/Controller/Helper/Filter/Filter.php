<?php

/*
 * Mendo Framework
 *
 * (c) Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mendo\Mvc\Controller\Helper\Filter;

use Mendo\Mvc\Controller\Helper\ActionHelperInterface;
use Mendo\Filter\FilterFunnelFactory;

/**
 * @author Mathieu Decaffmeyer <mdecaffmeyer@gmail.com>
 */
class Filter implements ActionHelperInterface
{
    private $funnelFactory;

    public function __construct(FilterFunnelFactory $funnelFactory)
    {
        $this->funnelFactory = $funnelFactory;
    }

    public function filter($value = null, $filters = null)
    {
        $funnel = $this->funnelFactory->createFunnel();

        if (!$value) {
            return $funnel;
        }

        return $funnel->filter($value, $filters);
    }
}
