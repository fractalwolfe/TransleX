<?php
/**
 * TransleX
 *
 * Copyright 2012-2013 by Joe Molloy <http://www.hyper-typer.com>
 * and Joakim Nyman <joakim@fractalwolfe.com>
 *
 * TransleX is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * TransleX is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Login; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package translex
 */
/**
 * TransleX utility class
 *
 * @package translex
 */
class TransleX {
    /** @var TransleXController $controller */
    public $controller;

    /**
     * Creates an instance of the TransleX class.
     *
     * @param modX &$modx A reference to the modX instance.
     * @param array $config An array of configuration parameters.
     * @return TransleX
     */
    function __construct(modX &$modx,array $config = array()) {
        $this->modx =& $modx;
        $modxCorePath = $modx->getOption('core_path',null,MODX_CORE_PATH);
        $corePath = $modx->getOption('translex.core_path',$config,$modx->getOption('core_path',null,MODX_CORE_PATH).'components/translex/');
        $this->config = array_merge(array(
            'chunksPath' => $corePath.'chunks/',
            'controllersPath' => $corePath.'controllers/',
            'corePath' => $corePath,
            'modelPath' => $corePath.'model/',
            'processorsPath' => $corePath.'processors/',
            'workspacePath' => $corePath.'workspace/',
            'packagesPath' => $modxCorePath.'components/',
        ),$config);

        $this->modx->addPackage('translex',$this->config['modelPath']);
        if ($this->modx->lexicon) {
            $this->modx->lexicon->load('translex:default');
        }
    }

    /**
     * Load the appropriate controller
     * @param string $controller
     * @return null|TransleXController
     */
    public function loadController($controller) {
        if ($this->modx->loadClass('TransleXController',$this->config['modelPath'].'translex/',true,true)) {
            $classPath = $this->config['controllersPath'].'web/'.$controller.'.php';
            $className = 'TransleX'.$controller.'Controller';
            if (file_exists($classPath)) {
                if (!class_exists($className)) {
                    $className = require_once $classPath;
                }
                if (class_exists($className)) {
                    $this->controller = new $className($this,$this->config);
                } else {
                    $this->modx->log(modX::LOG_LEVEL_ERROR,'[TransleX] Could not load controller: '.$className.' at '.$classPath);
                }
            } else {
                $this->modx->log(modX::LOG_LEVEL_ERROR,'[TransleX] Could not load controller file: '.$classPath);
            }
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[TransleX] Could not load TransleXController class.');
        }
        return $this->controller;
    }

    public function notifyAdmin($adminEmail,$action,$package,$topic,$lang,$site_name) {
        $user = $this->modx->user;
        if ($user->get('id') == 0) {
            $name = $this->modx->lexicon('translex.anonymous_user');
            $email = $this->modx->lexicon('translex.email_unknown');
        } else {
            $profile = $user->getOne('Profile');
            $name = $profile->get('fullname');
            $email = $profile->get('email');
        }
        $params = array();
        $params['action'] = $action;
        $params['name'] = $name;
        $params['email'] = $email;
        $params['package'] = $package;
        $params['topic'] = $topic;
        $params['lang'] = $lang;
        $params['site_name'] = $site_name;
        $message = $this->modx->lexicon('translex.admin_notify_email',$params);
        $subject = $this->modx->lexicon('translex.admin_notify_email_subject',array('site_name'=>$site_name));
        $from = $adminEmail;
        $fromName = 'TransleX';

        $this->modx->getService('mail', 'mail.modPHPMailer');
        $this->modx->mail->set(modMail::MAIL_BODY,$message);
        $this->modx->mail->set(modMail::MAIL_FROM,$from);
        $this->modx->mail->set(modMail::MAIL_FROM_NAME,$fromName);
        $this->modx->mail->set(modMail::MAIL_SENDER,$fromName);
        $this->modx->mail->set(modMail::MAIL_SUBJECT,$subject);
        $this->modx->mail->address('to',$adminEmail);
        $this->modx->mail->address('reply-to',$adminEmail);
        $this->modx->mail->setHTML(true);
        if (!$this->modx->mail->send()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,$this->modx->lexicon('translex.admin_notify_email_send_failure_message').' '.$this->modx->mail->mailer->ErrorInfo);

        }
        $this->modx->mail->reset();
    }

    /**
     * Helper function to get a chunk or tpl by different methods.
     *
     * @access public
     * @param string $name The name of the tpl/chunk.
     * @param array $properties The properties to use for the tpl/chunk.
     * @param string $type The type of tpl/chunk. Can be embedded,
     * modChunk, file, or inline. Defaults to modChunk.
     * @return string The processed tpl/chunk.
     */
    public function getChunk($name,$properties,$type = 'modChunk') {
        $output = '';
        switch ($type) {
            case 'embedded':
                if (!$this->modx->user->isAuthenticated($this->modx->context->get('key'))) {
                    $this->modx->setPlaceholders($properties);
                }
                break;
            case 'modChunk':
                $output .= $this->modx->getChunk($name, $properties);
                break;
            case 'file':
                $name = str_replace(array(
                    '{base_path}',
                    '{assets_path}',
                    '{core_path}',
                ),array(
                    $this->modx->getOption('base_path'),
                    $this->modx->getOption('assets_path'),
                    $this->modx->getOption('core_path'),
                ),$name);
                $output .= file_get_contents($name);
                $this->modx->setPlaceholders($properties);
                break;
            case 'inline':
            default:
                /* default is inline, meaning the tpl content was provided directly in the property */
                $chunk = $this->modx->newObject('modChunk');
                $chunk->setContent($name);
                $chunk->setCacheable(false);
                $output .= $chunk->process($properties);
                break;
        }
        return $output;
    }
}