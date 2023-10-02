<?php
declare(strict_types=1);

/**
 * AppController
 */

namespace Sis\Stats\Controller;

use Sis\Users\Auth\AuthPrefixTrait;

/**
 * App Controller
 *
 * Base controller for this plugin.
 *
 * @property \Sis\Core\Controller\Component\FlashComponent $Flash
 */
class AppController extends \Sis\Core\Controller\BaseController
{
    /**
     * Loads the Authentication and authorization settings/objects.
     */
    use AuthPrefixTrait;

    /**
     * @var string Prefix for the controllers.
     */
    public $pluginPrefix = 'stats';

    /**
     * Initialize method.
     *
     * @return void
     */
    public function initialize(): void
    {
        // Existing code
        parent::initialize();

        //load the configured auth component.
        $this->loadAuthComponent();
    }
}
