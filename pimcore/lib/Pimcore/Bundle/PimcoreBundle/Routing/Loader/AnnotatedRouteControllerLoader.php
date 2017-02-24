<?php


namespace Pimcore\Bundle\PimcoreBundle\Routing\Loader;

use Sensio\Bundle\FrameworkExtraBundle\Routing\AnnotatedRouteControllerLoader as BaseAnnotatedRouteControllerLoader;

/**
 * Normalizes autogenerated admin routes to pimcore_admin_ prefix
 */
class AnnotatedRouteControllerLoader extends BaseAnnotatedRouteControllerLoader
{
    /**
     * @inheritDoc
     */
    protected function getDefaultRouteName(\ReflectionClass $class, \ReflectionMethod $method)
    {
        $routeName = parent::getDefaultRouteName($class, $method);
        $routeName = str_replace('pimcore_pimcoreadmin_', 'pimcore_admin_', $routeName);

        return $routeName;
    }
}
