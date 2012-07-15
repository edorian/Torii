<?php
/**
 * This file is part of Torii
 *
 * @version $Revision$
 */

namespace Torii;
use Qafoo\RMF;

require __DIR__ . '/../main/Torii/bootstrap.php';
$dic = new DIC\Base();
$dic->environment = 'development';

$dispatcher = new RMF\Dispatcher\Simple(
    new RMF\Router\Regexp( array(
        // Torii main actions
        '(^/portal$)' => array(
            'GET'  => array( $dic->mainController, 'view' ),
        ),
        '(^/portal/settings$)' => array(
            'POST' => array( $dic->mainController, 'settings' ),
            'GET'  => array( $dic->mainController, 'settings' ),
        ),

        // Auth related actions
        '(^/$)' => array(
            'GET'  => array( $dic->authController, 'login' ),
        ),
        '(^/auth/login$)' => array(
            'POST'  => array( $dic->authController, 'login' ),
        ),
        '(^/auth/register$)' => array(
            'POST'  => array( $dic->authController, 'register' ),
        ),
        '(^/auth/confirm/(?P<user>[a-f0-9]+)/(?P<hash>[a-f0-9]+)$)' => array(
            'GET'  => array( $dic->authController, 'confirm' ),
        ),
        '(^/auth/logout$)' => array(
            'GET'  => array( $dic->authController, 'logout' ),
        ),
        '(^/auth/forgot$)' => array(
            'GET'  => array( $dic->authController, 'showForgot' ),
            'POST'  => array( $dic->authController, 'forgot' ),
        ),
    ) ),
    $dic->view
);

$request = new RMF\Request\HTTP();
$request->addHandler( 'body', new RMF\Request\PropertyHandler\PostBody() );
$request->addHandler( 'session', new RMF\Request\PropertyHandler\Session() );

$dispatcher->dispatch( $request );
