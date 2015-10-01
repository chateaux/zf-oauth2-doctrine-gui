<?php
namespace DoctrineGui\View\Helper;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

use Zend\View\Helper\AbstractHelper;

class DoctrineGuiNavHelper extends AbstractHelper
{

    public function __invoke()
    {

        $sidebarPage = $_SERVER['REQUEST_URI'];
        if (substr($sidebarPage,0,1) == '/') {
            $sidebarPage = substr($sidebarPage,1);
        }
        $parts  = explode('/',$sidebarPage);
        $child  = strtolower(isset($parts[1]) ? $parts[1] : '');

        $active['overview']   = '';
        $active['clients']    = '';
        $active['tests']      = '';

        if ( $child === 'overview')
        {
            $active['overview'] = 'active';
        }

        if ( $child === 'clients')
        {
            $active['clients'] = 'active';
        }

        echo '<div id="navbarCollapse" class="collapse navbar-collapse">
                <ul class="">
                    <li class="'.$active['overview'].'"><a href="'.$this->getView()->url('zf-oauth-doctrine-gui/overview').'">Overview</a></li>
                    <li class="'.$active['clients'].'"><a href="'.$this->getView()->url('zf-oauth-doctrine-gui/clients').'">Client</a></li>
                </ul>
            </div>';

    }
}
