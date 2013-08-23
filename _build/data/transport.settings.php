<?php
$settings = array();

$settings['translex.request_param_action']= $modx->newObject('modSystemSetting');
$settings['translex.request_param_action']->fromArray(array(
    'key' => 'translex.request_param_action',
    'value' => 'action',
    'xtype' => 'textfield',
    'namespace' => 'translex',
    'area' => 'gateway',
),'',true,true);

$settings['translex.request_param_obtain']= $modx->newObject('modSystemSetting');
$settings['translex.request_param_obtain']->fromArray(array(
    'key' => 'translex.request_param_obtain',
    'value' => 'obtain',
    'xtype' => 'textfield',
    'namespace' => 'translex',
    'area' => 'gateway',
),'',true,true);

$settings['translex.request_param_package']= $modx->newObject('modSystemSetting');
$settings['translex.request_param_package']->fromArray(array(
    'key' => 'translex.request_param_package',
    'value' => 'package',
    'xtype' => 'textfield',
    'namespace' => 'translex',
    'area' => 'gateway',
),'',true,true);

$settings['translex.request_param_topic']= $modx->newObject('modSystemSetting');
$settings['translex.request_param_topic']->fromArray(array(
    'key' => 'translex.request_param_topic',
    'value' => 'topic',
    'xtype' => 'textfield',
    'namespace' => 'translex',
    'area' => 'gateway',
),'',true,true);

$settings['translex.request_param_language']= $modx->newObject('modSystemSetting');
$settings['translex.request_param_language']->fromArray(array(
    'key' => 'translex.request_param_language',
    'value' => 'language',
    'xtype' => 'textfield',
    'namespace' => 'translex',
    'area' => 'gateway',
),'',true,true);

$settings['translex.languages']= $modx->newObject('modSystemSetting');
$settings['translex.languages']->fromArray(array(
    'key' => 'translex.languages',
    'value' => 'be,cs,en,es,fi,fr,ja,it,nl,pl,pt,ru,sv,th,zh',
    'xtype' => 'textfield',
    'namespace' => 'translex',
    'area' => 'language',
),'',true,true);

return $settings;