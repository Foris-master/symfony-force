<?php

namespace AdminBundle\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;

class MenuBuilder extends ContainerAware
{
    /**
     * @param FactoryInterface $factory
     * @return \Knp\Menu\ItemInterface
     */
    public function user(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav pull-right');

        $child = function($label, $route) use($menu) {
            $attributes = ['role' => 'presentation'];
            $menu->addChild($this->container->get('translator')->trans($label, [], 'menu'), compact('route', 'attributes'));
        };

        if ($this->getToken()->getUser() instanceof UserInterface) {
            $child($this->getToken()->getUser(), 'app_user_profile');
            $child('logout', 'app_user_logout');
        }

        return $menu;
    }

    /**
     * @param FactoryInterface $factory
     * @return \Knp\Menu\ItemInterface
     */
    public function main(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');
        $menu->setChildrenAttribute('class', 'nav navbar-nav');

        $child = function($label, $route) use($menu) {
            $attributes = ['role' => 'presentation'];
            $menu->addChild($this->container->get('translator')->trans($label, [], 'menu'), compact('route', 'attributes'));
        };

        $child('users', 'admin_user_index');
        $child('templates', 'admin_mailtemplate_index');
        $child('audit', 'admin_audit_index');
        $child('cms', 'admin_cmsblock_index');

        return $menu;
    }

    /**
     * Get Security Token Storage.
     * @return TokenInterface
     */
    private function getToken()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        $token = $this->container->get('security.token_storage')->getToken();
        if (!$token instanceof TokenInterface) {
            return null;
        }

        return $token;
    }
}
