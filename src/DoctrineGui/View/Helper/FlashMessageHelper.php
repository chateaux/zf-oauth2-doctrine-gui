<?php

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

namespace DoctrineGui\View\Helper;

use Zend\View\Helper\AbstractHelper;


class FlashMessageHelper extends AbstractHelper {

    public function __invoke()
    {
        $flash = $this->getView()->flashMessenger();
        echo $flash->render('error',   array('alert', 'alert-dismissable', 'alert-danger'));
        echo $flash->render('info',    array('alert', 'alert-dismissable', 'alert-info'));
        echo $flash->render('default', array('alert', 'alert-dismissable', 'alert-warning'));
        echo $flash->render('success', array('alert', 'alert-dismissable', 'alert-success'));
    }
}