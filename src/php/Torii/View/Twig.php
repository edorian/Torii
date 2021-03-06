<?php
/**
 * This file is part of Torii
 *
 * @version $Revision$
 */

namespace Torii\View;

use Torii\Struct;
use Qafoo\RMF\Request;

/**
 * Base MySQLi connection class
 *
 * @version $Revision$
 */
class Twig extends \Qafoo\RMF\View
{
    /**
     * Twig envoronment
     *
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * Construct from twig environment
     *
     * @param \Twig_Environment $twig
     * @return void
     */
    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Display the controller result
     *
     * @param Request $request
     * @param mixed $result
     * @return void
     */
    public function display(Request $request, $result)
    {
        if ($result instanceof \Exception) {
            echo $this->twig->render(
                'html/error.twig',
                array(
                    'exception' => $result,
                )
            );
            return;
        }

        echo $this->twig->render(
            'html/' . $result->template,
            $result->data
        );
    }
}
