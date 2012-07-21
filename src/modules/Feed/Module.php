<?php
/**
 * This file is part of Torii
 *
 * @version $Revision: 1469 $
 */

namespace Torii\Module\Feed;

use Qafoo\RMF;
use Torii\Struct;
use Torii\DIC;
use Torii\Assets;

/**
 * Feed module definition
 *
 * @version $Revision$
 */
class Module extends \Torii\Module
{
    /**
     * Get module summary
     *
     * @return Struct\Module
     */
    public function getSummary()
    {
        return new Struct\Module(
            'Feed',
            'Provides access to a number of RSS / Atom feeds'
        );
    }

    /**
     * Execute action
     *
     * Should, most probably dispatch to own controller implementation.
     *
     * @param RMF\Request $request
     * @param Struct\User $user
     * @return Struct\Response
     */
    public function handle( RMF\Request $request, Struct\User $user )
    {
        throw new \RuntimeException( '@TODO: Implement' );
    }

    /**
     * Inject DIC
     *
     * This is the interface between modules and the base system. Module
     * definitions are not supposed to be re-usable in other systems.
     *
     * The DIC in this case provides the means to configure your own module
     * environment. The DIC schould NOT be passed to any other objects.
     *
     * @param DIC $dic
     * @return void
     */
    public function initialize( DIC $dic )
    {
        parent::initialize( $dic );

        // Register path for custom templates
        $dic->twig->getLoader()->addPath( __DIR__ . '/templates' );

        // Register assets
        $dic->css->addFileSet( new Assets\FileSet( __DIR__ . '/css', '*.css' ) );
        $dic->javaScript->addFileSet( new Assets\FileSet( __DIR__ . '/js', '*.js' ) );
        $dic->images->addFileSet( new Assets\FileSet( __DIR__ . '/images', '*.png' ) );
    }
}

// Important for registration
return new Module();

