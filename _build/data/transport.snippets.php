<?php
$snippets = array();

$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 1,
    'name' => 'TransleX',
    'description' => 'Renders the TransleX interface on a blank resource.',
    'snippet' => getSnippetContent($sources['snippets'],'translex.snippet'),
),'',true,true);
$properties = include $sources['properties'].'properties.translex.php';
$snippets[1]->setProperties($properties);
unset($properties);

return $snippets;