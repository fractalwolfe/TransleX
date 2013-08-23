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
 * Handles saving of translation to file and making it live
 *
 * @package translex
 */
class TransleXTranslationCommitProcessor extends TransleXProcessor {
    public $package = '';
    public $topic = '';
    public $lang = '';
    public $keys = array();
    public $response = array();

    public function process() {
        $this->processRequest();
        $this->writeFile();

        // Get filename
        $lfile = ($this->package == 'core') ? $this->modx->getOption('core_path') : $this->translex->config['packagesPath'].$this->package;
        $lfile .= '/lexicon/'.$this->lang.'/'.$this->topic.'.inc.php';

        // Backup old file
        $bfile = $this->translex->config['workspacePath'].$this->package.'/'.$this->lang.'/'.time().'-'.$this->topic.'.inc.php.bk';
        $bk = copy($lfile,$bfile);
        if (!$bk) {
            // If backup failed, log event and respond
            $this->controller->logEvent('commit',$this->modx->lexicon('translex.error_backup_failed'),$this->package,$this->topic,$this->lang);
            $response['success'] = 0;
            $response['message'] = $this->modx->lexicon('translex.error_backup_failed');
            return $this->controller->responseToJSON($response);
        } else {
            // If backup succeeded, log event and try to replace live file
            $this->controller->logEvent('commit',$this->modx->lexicon('translex.success_backup_completed').' - '.$bfile,$this->package,$this->topic,$this->lang);

            $wfile = $this->translex->config['workspacePath'].$this->package.'/'.$this->lang.'/'.$this->topic.'.inc.php';
            $ct = copy($wfile,$lfile);
            if (!$ct) {
                // If replacing the file failed, log the event and respond
                $this->controller->logEvent('error',$this->modx->lexicon('translex.error_commit_failed').' - '.$wfile,$this->package,$this->topic,$this->lang);
                $response['success'] = 0;
                $response['message'] = $this->modx->lexicon('translex.error_commit_failed');
                return $this->controller->responseToJSON($response);
            } else {
                // If replacing the file succeeded, log the event and respond
                $this->controller->logEvent('commit',$this->modx->lexicon('translex.success_commit_completed').' - '.$wfile,$this->package,$this->topic,$this->lang);
                $response['success'] = 1;
                $response['message'] = $this->modx->lexicon('translex.success_commit_completed');
                return $this->controller->responseToJSON($response);
            }
        }
    }

    public function processRequest() {
        $this->package = $_POST[$this->controller->getProperty('request_param_package')];
        $this->topic = $_POST[$this->controller->getProperty('request_param_topic')];
        $this->lang = $_POST[$this->controller->getProperty('request_param_language')];
        $this->lang = str_replace(' ('.$this->modx->lexicon('translex.default').')','',$this->lang);
        $post = $this->controller->getRealPOST();

        foreach($post as $key => $value){
            if($key != $this->controller->getProperty('request_param_package')
                && $key != $this->controller->getProperty('request_param_topic')
                && $key != $this->controller->getProperty('request_param_language')
                && $key != $this->controller->getProperty('request_param_action')) {
                $keys[$key] = $value;
            }
        }
        return true;
    }

    public function writeFile() {
        try {
            $file = fopen($this->translex->config['workspacePath'].$this->package.'/'.$this->lang.'/'.$this->topic.'.inc.php','wt');
            fwrite($file,"<?php\n");
            foreach($this->keys as $key => $value){
                fwrite($file,'$_lang[\''.$key.'\'] = \''.str_replace("'","\'",$value).'\';'."\n");
            }
            fclose($file);
        } catch (Exception $e) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[TransleX] '.$this->modx->lexicon('translex.error_writing_topic_file_failed'));
            return false;
        }
        // Log event
        $this->controller->logEvent('save','',$this->package,$this->topic,$this->lang);
        return true;
    }
}