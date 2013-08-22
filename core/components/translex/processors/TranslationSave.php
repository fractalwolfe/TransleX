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
 * Handles saving of translation to file
 *
 * @package translex
 */
class TransleXTranslationSaveProcessor extends TransleXProcessor {
    public $package = '';
    public $topic = '';
    public $lang = '';
    public $keys = array();
    public $response = array();

    public function process() {
        $this->processRequest();

        $file = fopen($this->translex->config['workspacePath'].$this->package.'/'.$this->lang.'/'.$this->topic.'.inc.php','wt');
        if ($file) {
            fwrite($file,"<?php\n");
            foreach($this->keys as $key => $value) {
                fwrite($file,'$_lang[\''.$key.'\'] = \''.str_replace("'","\'",$value).'\';'."\n");
            }
            fclose($file);

            // Log event
            $this->controller->logEvent('save','',$this->package,$this->topic,$this->lang);

            $notifyTo = $this->controller->getProperty('notifyTo');
            if (!empty($notifyTo)) {
                $action = $this->modx->lexicon('translex.event_saved');
                $package = ucwords($_POST[$this->controller->getProperty('packageKey')]);
                $topic = ucwords($_POST[$this->controller->getProperty('topicKey')]);
                $lang = ucwords($_POST[$this->controller->getProperty('languageKey')]);
                $site_name = $this->modx->getOption('site_name');
                $inst = $action.$package.$topic.$lang;
                if (!isset($_COOKIE['translex'])) {
                    $insts[0] = $inst;
                    $instsSER = serialize($insts);
                    setcookie('translex',$instsSER);
                } else {
                    $instsSER = $_COOKIE['translex'];
                    $insts = unserialize($instsSER);
                    if (is_array($insts)) {
                        if (!in_array($inst,$insts)) {
                            $insts[] = $inst;
                            $instsJSON = json_encode($insts);
                            setcookie('translex',$instsJSON);
                        }
                    }
                }
                $this->translex->notifyAdmin($notifyTo,$action,$package,$topic,$lang,$site_name);
            }
            $response['success'] = 1;
            $response['message'] = $this->modx->lexicon('translex.saved_message');
            return $this->controller->responseToJSON($response);
        } else {
            $error_message = $this->modx->lexicon('translex_saved_message_error');
            $this->controller->logEvent('save',$error_message,$this->package,$this->topic,$this->lang);
            $response['success'] = 0;
            $response['message'] = $error_message;
            return $this->controller->responseToJSON($response);
        }
    }

    public function processRequest() {
        $this->package = $_POST[$this->controller->getProperty('packageKey')];
        $this->topic = $_POST[$this->controller->getProperty('topicKey')];
        $this->lang = $_POST[$this->controller->getProperty('languageKey')];
        $this->lang = str_replace(' ('.$this->modx->lexicon('translex.default').')','',$this->lang);
        $post = $this->controller->getRealPOST();

        foreach($post as $key => $value){
            if($key != $this->controller->getProperty('packageKey')
                && $key != $this->controller->getProperty('topicKey')
                && $key != $this->controller->getProperty('languageKey')
                && $key != $this->controller->getProperty('actionKey')) {
                $keys[$key] = $value;
            }
        }
        return true;
    }
}