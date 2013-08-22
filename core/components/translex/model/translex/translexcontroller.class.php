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
 * Class TransleXController
 *
 * @package translex
 * @subpackage request
 */
abstract class TransleXController {
    /** @var modX $modx */
    public $modx;
    /** @var TransleX $translex */
    public $translex;
    /** @var array $config */
    public $config = array();
    /** @var array $scriptProperties */
    protected $scriptProperties = array();
    /** @var array $placeholders */
    protected $placeholders = array();

    /**
     * @param TransleX $translex A reference to the TransleX instance
     * @param array $config
     */
    function __construct(TransleX &$translex,array $config = array()) {
        $this->translex =& $translex;
        $this->modx =& $translex->modx;
        $this->config = array_merge($this->config,$config);
    }

    public function run($scriptProperties) {
        $this->setProperties($scriptProperties);
        $this->initialize();
        return $this->process();
    }

    abstract public function initialize();
    abstract public function process();

    /**
     * Set the default options for this module
     * @param array $defaults
     * @return void
     */
    protected function setDefaultProperties(array $defaults = array()) {
        $this->scriptProperties = array_merge($defaults,$this->scriptProperties);
    }

    /**
     * Set an option for this module
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setProperty($key,$value) {
        $this->scriptProperties[$key] = $value;
    }
    /**
     * Set an array of options
     * @param array $array
     * @return void
     */
    public function setProperties($array) {
        foreach ($array as $k => $v) {
            $this->setProperty($k,$v);
        }
    }

    /**
     * Return an array of REQUEST options
     * @return array
     */
    public function getProperties() {
        return $this->scriptProperties;
    }

    /**
     * @param $key
     * @param null $default
     * @param string $method
     * @return mixed
     */
    public function getProperty($key,$default = null,$method = '!empty') {
        $v = $default;
        switch ($method) {
            case 'empty':
            case '!empty':
                if (!empty($this->scriptProperties[$key])) {
                    $v = $this->scriptProperties[$key];
                }
                break;
            case 'isset':
            default:
                if (isset($this->scriptProperties[$key])) {
                    $v = $this->scriptProperties[$key];
                }
                break;
        }
        return $v;
    }

    public function setPlaceholder($k,$v) {
        $this->placeholders[$k] = $v;
    }
    public function getPlaceholder($k,$default = null) {
        return isset($this->placeholders[$k]) ? $this->placeholders[$k] : $default;
    }
    public function setPlaceholders($array) {
        foreach ($array as $k => $v) {
            $this->setPlaceholder($k,$v);
        }
    }
    public function getPlaceholders() {
        return $this->placeholders;
    }

    /**
     * @param string $processor
     * @return mixed|string
     */
    public function runProcessor($processor,array $data = array()) {
        $output = '';
        $processor = $this->loadProcessor($processor);
        if (empty($processor)) return $output;

        return $processor->process($data);
    }

    /**
     * @param $processor
     * @return bool|TransleXProcessor
     */
    public function loadProcessor($processor) {
        $processorFile = $this->config['processorsPath'].$processor.'.php';
        if (!file_exists($processorFile)) {
            return false;
        }
        try {
            $className = 'TransleX'.ucfirst($processor).'Processor';
            if (!class_exists($className)) {
                $className = include_once $processorFile;
            }
            /** @var TransleXProcessor $processor */
            $processor = new $className($this->translex,$this);
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR,'[TransleX] '.$e->getMessage());
        }
        return $processor;
    }

    /**
     * Logs an event using the LogWrite processor
     *
     * @param $message
     * @param $action
     * @param $package
     * @param $topic
     * @param $lang
     * @return bool|string
     */
    public function logEvent($action,$message,$package,$topic,$lang) {
        if ($this->getProperty('log') != null) {
            if (in_array($action,$this->getProperty('log'))){
                $data = array(
                    'action' => $action,
                    'message' => $message,
                    'package' => $package,
                    'topic' => $topic,
                    'lang' => $lang
                );
                return $this->runProcessor('LogWrite',$data);
            }
        }
        return true;
    }

    public function responseToJSON($response) {
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        $json = json_encode($response);
        return $json;
    }

    public function getRealPOST() {
        $pairs = explode("&", file_get_contents("php://input"));
        $vars = array();
        foreach ($pairs as $pair) {
            $nv = explode("=", $pair);
            $name = urldecode($nv[0]);
            $value = urldecode($nv[1]);
            $vars[$name] = $value;
        }
        return $vars;
    }

    public function escapePlaceholders($str) {
        return str_replace('[[+', '&#91;[+',$str);
    }

    public function htmlEncode($var) {
        return htmlentities($var, ENT_QUOTES, 'UTF-8');
    }
}

/**
 * Abstracts processors into a class
 * @package translex
 */
abstract class TransleXProcessor {
    /** @var TransleX $translex */
    public $translex;
    /** @var TransleXController $controller */
    public $controller;
    /** @var array $config */
    public $config = array();

    /**
     * @param TransleX &$translex A reference to the TransleX instance
     * @param TransleXController &$controller
     * @param array $config
     */
    function __construct(TransleX &$translex,TransleXController &$controller,array $config = array()) {
        $this->translex =& $translex;
        $this->modx =& $translex->modx;
        $this->controller =& $controller;
        $this->config = array_merge($this->config,$config);
    }

    abstract function process();
}