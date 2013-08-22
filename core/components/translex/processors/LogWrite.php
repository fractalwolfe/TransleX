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
class TransleXLogWriteProcessor extends TransleXProcessor {
    /**
     * @return boolean|string
     */
    public function process(array $data = array()) {
        $user = $this->modx->user;
        if ($user->get('id') == 0) {
            $name = $this->modx->lexicon('translex.anonymous_user');
            $email = $this->modx->lexicon('translex.email_unknown');
        } else {
            $profile = $user->getOne('Profile');
            $name = $profile->get('fullname');
            $email = $profile->get('email');
        }

        $logstr = '';
        $logstr .= $this->modx->lexicon('translex.user').': '.$name.' <'.$email.'>';

        if (!empty($data['package'])) {
            $logstr .= ' - '.$this->modx->lexicon('translex.package').': '.$data['package'];
        }
        if (!empty($data['topic'])) {
            $logstr .= ' - '.$this->modx->lexicon('translex.topic').': '.$data['topic'];
        }
        if (!empty($data['lang'])) {
            $logstr .= ' - '.$this->modx->lexicon('translex.language').': '.$data['lang'];
        }
        if (!empty($data['message'])) {
            $logstr .= ' :: '.$data['message'];
        }
        switch($data['action']) {
            case 'error':
                $logstr = $this->modx->lexicon('translex.event_error').' :: '.$logstr;
                break;
            case 'save':
                $logstr = $this->modx->lexicon('translex.event_saved').' :: '.$logstr;
                break;
            case 'commit':
                $logstr = $this->modx->lexicon('translex.event_committed').' :: '.$logstr;
                break;
            case 'access':
                $logstr = $this->modx->lexicon('translex.event_accessed').' :: '.$logstr;
                break;
        }

        $logstr = $this->modx->lexicon('translex.settings_header').' :: '.$logstr;
        $logfile = fopen($this->translex->config['workspacePath'].'translex.log','a');
        if (!$logfile) {
            return false;
        } else {
            $logstr = date('Y-m-d G:i:s').' '.$logstr;
            if(!empty($_SERVER['REMOTE_ADDR'])){
                $logstr .= ' :: '.$this->modx->lexicon('translex.remote_host').': '.$_SERVER['REMOTE_ADDR']."\n";
            }
            fwrite($logfile,$logstr);
            fclose($logfile);
            return true;
        }
    }
}
return 'TransleXLogWriteProcessor';