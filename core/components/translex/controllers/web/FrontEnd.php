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
 * TransleX Front-End Controller
 *
 * @package translex
 * @subpackage controllers
 */
class TransleXFrontEndController extends TransleXController {
    /** @var array $packages List of available packages */
    public $packages = array();
    /** @var array $languages List of available languages */
    public $languages = array();

    public function initialize() {
        $this->setDefaultProperties(array(
            'tpl' => 'translexInterface',
            'pkgWrapperTpl' => 'translexPackageContainer',
            'pkgItemTpl' => 'translexPackageItem',
            'langWrapperTpl' => 'translexLanguageContainer',
            'langItemTpl' => 'translexLanguageItem',
            'topicWrapperTpl' => 'translexTopicContainer',
            'cultureKey' => $this->modx->cultureKey,
            'packages' => '',
            'topics' => '',
            'languages' => $this->modx->getOption('translex.languages',null,'be,cs,en,es,fi,fr,ja,it,nl,pl,pt,ru,sv,th,zh'),
            'viewCore' => false,
            'notifyTo' => '',
            'log' => '',

            'request_param_action' => $this->modx->getOption('translex.request_param_action',null,'action'),
            'request_param_obtain' => $this->modx->getOption('translex.request_param_obtain',null,'obtain'),
            'request_param_package' => $this->modx->getOption('translex.request_param_package',null,'package'),
            'request_param_topic' => $this->modx->getOption('translex.request_param_topic',null,'topic'),
            'request_param_language' => $this->modx->getOption('translex.request_param_language',null,'language'),
        ));
    }

    /**
     * Process the controller
     * @return string
     */
    public function process() {
        $this->initLog();

        switch($_SERVER['REQUEST_METHOD']) {
            case 'POST':
                $content = $this->handleRequest();
                break;
            case 'GET':
            default:
                $content = $this->renderInterface();
                break;
        }
        return $content;
    }

    public function initLog() {
        $log = trim($this->getProperty('log'));
        if (!empty($log)) {
            $logOptions = explode(',',$log);
            $logOpts = array();
            foreach($logOptions as $option) {
                $logOpts[] = trim($option);
            }
            $this->setProperty('log',$logOpts);
        }
        return true;
    }

    /**
     * Handle requests
     *
     * @return string|bool
     */
    public function handleRequest() {
        $actionKey = $this->getProperty('request_param_action');

        $output = false;
        if (!empty($_POST) && isset($_POST[$actionKey])) {
            // If this is an action request
            switch ($_POST[$actionKey]) {
                case 'save':
                    $output = $this->runProcessor('TranslationSave');
                    break;
                case 'commit':
                    $output = $this->runProcessor('TranslationCommit');
                    break;
                case 'log':
                    $output = $this->runProcessor('LogRead');
                    break;
                case 'clearlog':
                    $output = $this->runProcessor('LogClear');
                    break;
            }
        }
        elseif (!empty($_POST) && !isset($_POST[$actionKey])) {
            // If this isn't an action request
            $output = $this->renderData();
        }
        else {
            $output = $this->renderInterface();
        }
        return $output;
    }

    public function getSettings() {
        $packagesPath = $this->translex->config['packagesPath'];
        $this->packages = $this->getPackages($packagesPath);
        $this->languages = $this->getLanguages();

        $cultureKey = $this->getProperty('cultureKey');
        if (!empty($cultureKey)) {
            $this->modx->setOption('cultureKey',$cultureKey);
        }
    }

    /**
     * Get a list of packages that can be translated
     *
     * @param $packagesPath
     * @return array $packages Available packages
     */
    public function getPackages($packagesPath) {
        $packagesStr = $this->getProperty('packages');
        $packages = array();

        $packageArray = (!empty($packagesStr)) ? explode(',',str_replace(' ','',$packagesStr)) : array();
        $packageDirs = glob($packagesPath.'*',GLOB_ONLYDIR);
        foreach ($packageDirs as $packageDir) {
            $packageName = str_replace($packagesPath,'',$packageDir);
            if (count($packageArray) > 0) {
                if (in_array($packageName,$packageArray)) {
                    $packages[] = $packageName;
                }
            } else {
                $packages[] = $packageName;
            }
        }

        // If MODX core package is requested, add that to the list
        $viewCore = $this->getProperty('viewCore');
        if ($viewCore == 1) {
            $packages[] = 'core';
        }

        sort($packages);
        return $packages;
    }

    /**
     * Get a list of languages packages can be translated into
     *
     * @return array $languages Available translations
     */
    public function getLanguages() {
        $languagesStr = $this->getProperty('languages');
        if (!empty($languagesStr)) {
            $languages = explode(',',str_replace(' ','',$languagesStr));
        } else {
            $languages = array();
        }
        return $languages;
    }

    /**
     * Load elements and build the interface
     *
     * @return string Returns the interface layout
     */
    public function renderInterface() {
        $this->getSettings();

        if(!file_exists($this->translex->config['workspacePath'])){
            $wd = mkdir($this->translex->config['workspacePath'],0777);
            if(!$wd){
                return '<p>'.$this->modx->lexicon('translex.error_create_workspacedir_failed').'</p>';
            }
        }

        $packages = '';
        foreach($this->packages as $packagekey){
            $packagename = $packagekey;
            // Separated to allow proper name for MODX Manager lexicon
            if ($packagekey == 'core') $packagename = "MODX Manager";
            $packages .= $this->modx->getChunk($this->getProperty('pkgItemTpl'),array('packagekey' => $packagekey, 'packagename' => $packagename));
        }
        $packagesWrapper = $this->modx->getChunk($this->getProperty('pkgWrapperTpl'),array('packages' => $packages));

        $languages = '';
        foreach($this->languages as $language){
            if($language == $this->modx->cultureKey){
                $language = $language.' ('.$this->modx->lexicon('translex.default').')';
            }
            $languages .= $this->modx->getChunk($this->getProperty('langItemTpl'),array('language' => $language));
        }
        $languagesWrapper = $this->modx->getChunk($this->getProperty('langWrapperTpl'),array('languages' => $languages));

        $topicsWrapper = $this->modx->getChunk($this->getProperty('topicWrapperTpl'));

        $elements = array(
            'packages' => $packagesWrapper,
            'languages' => $languagesWrapper,
            'topics' => $topicsWrapper
        );

        $lf = $this->logEvent('access','','','',$this->modx->cultureKey);
        if(!$lf){
            return '<p>'.$this->modx->lexicon('translex.error_create_logfile_failed').'</p>';
        }

        return $this->modx->getChunk($this->getProperty('tpl'),$elements);
    }

    public function renderData() {
        $response = array();
        $cultureKey = $this->getProperty('cultureKey');
        if (empty($cultureKey)) {
            $cultureKey = $this->modx->cultureKey;
        }
        if ($_POST[$this->getProperty('request_param_obtain')] == 'package') {
            if (empty($_POST[$this->getProperty('request_param_package')])) {
                $response['success'] = 0;
                $response['message'] = $this->modx->lexicon('translex.error_no_package_selected');
            } else {
                $defaultdir = ($_POST[$this->getProperty('request_param_package')] == "core") ? $this->modx->getOption('core_path') : $this->translex->config['packagesPath'];
                $defaultdir .= 'lexicon/'.$cultureKey.'/';
                if (!file_exists($defaultdir)) {
                    // If default language does not exist, log event
                    $this->logEvent('error',$this->modx->lexicon('translex.error_no_default_language'),$_POST[$this->getProperty('request_param_package')],'',$cultureKey);
                    $response['success'] = 0;
                    $response['message'] = $this->modx->lexicon('translex.error_no_default_language');
                } else {
                    // If default language exists, load topics
                    $topics = array();
                    $topicfiles = glob($defaultdir.'*.php');
                    $topicsstr = $this->getProperty('topics');
                    if (empty($topicsAr)) {
                        $topicsAr = array();
                    } else {
                        $topicsAr = explode(',',str_replace(' ','',$topicsstr));
                    }
                    foreach($topicfiles as $file) {
                        $topicfile = basename($file);
                        $topic = str_replace('.inc.php','',$topicfile);
                        if (count($topicsAr) > 0) {
                            // If user has specified topics to load, iterate through them
                            if (in_array($topic,$topicsAr)) {
                                // If match is found, load the topic
                                $topics[] = $topic;
                            }
                        } else {
                            // If no topics have been specified, load them all
                            $topics[] = $topic;
                        }
                    }
                    if (count($topics) == 0) {
                        // If no topics were found, log event
                        $message = $this->modx->lexicon('translex.error_no_default_topics');
                        $package = $_POST[$this->getProperty('request_param_package')];
                        $topic = $_POST[$this->getProperty('request_param_topic')];
                        $this->logEvent('error',$message,$package,$topic,$cultureKey);
                        $response['success'] = 0;
                        $response['message'] = $this->modx->lexicon('translex.error_no_default_topics');
                    } else {
                        // Return topics
                        $response['success'] = 1;
                        $response['topics'] = $topics;
                    }
                }
            }
        } else {
            $package = $_POST[$this->getProperty('request_param_package')];
            $topic = $_POST[$this->getProperty('request_param_topic')];
            $base_path = ($package == 'core') ? $this->modx->getOption('core_path') : $this->translex->config['packagesPath'].$package;
            include($base_path.'/lexicon/'.$cultureKey.'/'.$topic.'.inc.php');

            if (count($_lang) == 0) {
                // If topic has no entries, log event and return error
                $error_message = $this->modx->lexicon('translex.error_no_default_topic_entries');
                $this->logEvent('error',$error_message,$package,$topic,$cultureKey);
                $response['success'] = 0;
                $response['message'] = $error_message;
            } else {
                // Iterate through entries, escape placeholders and return data
                $rows = array();
                foreach($_lang as $key => $value) {
                    $row['key'] = $key;
                    $row['value'] = nl2br($this->escapePlaceholders($this->htmlEncode($value)));
                    $rows[] = $row;
                }
                unset($_lang);
                $response['data'] = $rows;
            }
            if ($_POST[$this->getProperty('request_param_obtain')] == 'language') {
                $lang = $_POST[$this->getProperty('request_param_language')];
                $olang = array();
                if (!empty($lang)) {
                    $lang = str_replace(' ('.$this->modx->lexicon('translex.default').')','',$lang);
                    $olangdir = $base_path.'/lexicon/'.$lang.'/';
                    if (file_exists($olangdir)) {
                        if(!empty($topic)){
                            $_lang = array();
                            include($base_path.'/lexicon/'.$lang.'/'.$topic.'.inc.php');
                            $olang = $_lang;
                            unset($_lang);
                        }
                    }
                    // Check that package dir exists
                    $workingPkgDir = $this->translex->config['workspacePath'].$package;
                    if (!file_exists($workingPkgDir)) {
                        // If not, try to create it
                        $wpd = mkdir($workingPkgDir);
                        if(!$wpd){
                            $error_message = $this->modx->lexicon('translex.error_create_workspacepkgdir_failed').' - '.$workingPkgDir;
                            $this->logEvent('error',$error_message,$package,$topic,$cultureKey);
                            $response['success'] = 0;
                            $response['message'] = $this->modx->lexicon('translex.error_create_workspacepkgdir_failed');
                            return $this->responseToJSON($response);
                        }
                    }
                    // Check that language dir exists
                    $workingLangDir = $workingPkgDir.'/'.$lang.'/';
                    if (!file_exists($workingLangDir)) {
                        // If not, try to create it
                        $wld = mkdir($workingLangDir,0777);
                        if(!$wld){
                            $error_message = $this->modx->lexicon('translex.error_create_workspacelangdir_failed').' - '.$workingLangDir;
                            $this->logEvent('error',$error_message,$package,$topic,$cultureKey);
                            $response['success'] = 0;
                            $response['message'] = $this->modx->lexicon('translex.error_create_workspacelangdir_failed');
                            return $this->responseToJSON($response);
                        }
                    }
                    // Check that topic file exists
                    $workingFile = $workingLangDir.'/'.$topic.'.inc.php';
                    $flang = array();
                    if (!file_exists($workingFile)) {
                        if ($lang != $cultureKey) {
                            // If selected language is other than cultureKey,
                            // open the file for the selected language
                            $file = fopen($workingFile,'w');
                        } else {
                            // If selected language is same as cultureKey,
                            // make a copy of the live file
                            $liveFile = $base_path.'/lexicon/'.$lang.'/'.$topic.'.inc.php';
                            $file = copy($liveFile,$workingFile);
                        }
                        if (!$file) {
                            // If file still doesn't exist, log event and return error
                            $error_message = $this->modx->lexicon('translex.error_create_topicfile').' - '.$workingFile;
                            $this->logEvent('error',$error_message,$package,$topic,$cultureKey);
                            $response['success'] = 0;
                            $response['message'] = $this->modx->lexicon('translex.error_create_topicfile');
                            return $this->responseToJSON($response);
                        } else {
                            // Close the file and run again
                            @fclose($file);
                            return $this->renderData();
                        }
                    } else {
                        // File exists, read the topic entries
                        include_once($workingFile);

                        $wlang = $_lang;
                        if (count($wlang) > 0) {
                            foreach($wlang as $key => $value) {
                                $entry['key'] = $key;
                                $entry['values'] = array('working'=>$this->escapePlaceholders($value),'live'=>'');
                                $flang[] = $entry;
                            }
                            $response['success'] = 1;
                            $response['ready'] = 1;
                            $response['keys'] = $flang;
                        } else {
                            if ($lang == $cultureKey) {
                                // File empty and language matches cultureKey, delete it
                                $deleted = unlink($workingFile);
                                if ($deleted) {
                                    // On success, make a new copy from live file
                                    $liveFile = $base_path.'/lexicon/'.$lang.'/'.$topic.'.inc.php';
                                    $file = copy($liveFile,$workingFile);
                                    if (!$file) {
                                        // If file still doesn't exist, log event and return error
                                        $error_message = $this->modx->lexicon('translex.error_create_topicfile').' - '.$workingFile;
                                        $this->logEvent('error',$error_message,$package,$topic,$cultureKey);
                                        $response['success'] = 0;
                                        $response['message'] = $this->modx->lexicon('translex.error_create_topicfile');
                                        return $this->responseToJSON($response);
                                    } else {
                                        // Close the file and run again
                                        @fclose($file);
                                        return $this->renderData();
                                    }
                                } else {
                                    // Failed to delete file
                                    $error_message = $this->modx->lexicon('translex.error_remove_empty_topicfile_failed').' - '.$workingFile;
                                    $this->logEvent('error',$error_message,$package,$topic,$cultureKey);
                                    $response['success'] = 0;
                                    $response['message'] = $this->modx->lexicon('translex.error_remove_empty_topicfile_failed');
                                    return $this->responseToJSON($response);
                                }
                            } else {
                                // File empty but different language than cultureKey
                                if (count($olang) > 0) {
                                    foreach ($olang as $key => $value) {
                                        $entry['key'] = $key;
                                        if ($wlang[$key] != $value) {
                                            if ($wlang[$key] == null) {
                                                $wkey = $value;
                                            } else {
                                                $wkey = $wlang[$key];
                                            }
                                            $values = array('working'=>$this->escapePlaceholders($wkey), 'live'=>nl2br($this->escapePlaceholders($this->htmlEncode($value))));
                                        }else{
                                            $values = array('working'=>$this->escapePlaceholders($wlang[$key]),'live'=>nl2br($this->escapePlaceholders($this->htmlEncode($value))));
                                        }
                                        $entry['values'] = $values;
                                        $flang[] = $entry;
                                    }
                                    $response['success'] = 1;
                                    $response['ready'] = 1;
                                    $response['keys'] = $flang;
                                } else {
                                    if (count($wlang) > 0) {
                                        foreach($wlang as $key => $value) {
                                            $entry['key'] = $key;
                                            $entry['values'] = array('working'=>$this->escapePlaceholders($value),'live'=>'');
                                            $flang[] = $entry;
                                        }
                                    }
                                    $response['success'] = 1;
                                    $response['ready'] = 1;
                                    $response['keys'] = $flang;
                                }
                            }
                        }
                        $response['success'] = 1;
                        $response['ready'] = 1;
                        $response['keys'] = $flang;
                    }
                }
            }
        }
        return $this->responseToJSON($response);
    }
}
return 'TransleXFrontEndController';