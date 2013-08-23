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
 * Class TransleXLogReadProcessor
 *
 * @package translex
 */
class TransleXLogReadProcessor extends TransleXProcessor {
    public function process() {
        $response = array();
        $logfile = $this->translex->config['workspacePath'].'translex.log';
        $logEntries = array();

        if (file_exists($logfile)) {
            $fh = fopen($logfile,'r');
            while (!feof($fh)) {
                $logEntries[] = fgets($fh);
            }
            $logEntries = array_reverse($logEntries);
            $response['success'] = 1;
            if (count($logEntries) > 0) {
                $response['log'] = $logEntries;
            } else {
                $response['log'] = $logEntries;
                $response['message'] = $this->modx->lexicon('translex.log_file_empty');
            }
        } else {
            $response['success'] = 0;
            $response['message'] = $this->modx->lexicon('translex.error_logfile_does_not_exist');
        }
        $response['success'] = 1;
        return $this->translex->responseToJSON($response);
    }
}
return 'TransleXLogReadProcessor';


