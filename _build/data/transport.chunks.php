<?php
$chunks = array();

$chunks[1] = $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
    'id' => 1,
    'name' => 'translexInterface',
    'description' => 'The TransleX interface template.',
    'snippet' => file_get_contents($sources['chunks'].'translexinterface.html'),
));

$chunks[2] = $modx->newObject('modChunk');
$chunks[2]->fromArray(array(
    'id' => 2,
    'name' => 'translexJS',
    'description' => 'The chunk holding the javascript. Contains MODX tags that are parse on request.',
    'snippet' => file_get_contents($sources['chunks'].'translexjs.html'),
));

$chunks[3] = $modx->newObject('modChunk');
$chunks[3]->fromArray(array(
    'id' => 3,
    'name' => 'translexLanguageContainer',
    'description' => 'The wrapper template for language items.',
    'snippet' => file_get_contents($sources['chunks'].'translexlanguagecontainer.html'),
));

$chunks[4] = $modx->newObject('modChunk');
$chunks[4]->fromArray(array(
    'id' => 4,
    'name' => 'translexLanguageItem',
    'description' => 'The template for language items.',
    'snippet' => file_get_contents($sources['chunks'].'translexlanguageitem.html'),
));

$chunks[5] = $modx->newObject('modChunk');
$chunks[5]->fromArray(array(
    'id' => 5,
    'name' => 'translexPackageContainer',
    'description' => 'The wrapper template for package items.',
    'snippet' => file_get_contents($sources['chunks'].'translexpackagecontainer.html'),
));

$chunks[6] = $modx->newObject('modChunk');
$chunks[6]->fromArray(array(
    'id' => 6,
    'name' => 'translexPackageItem',
    'description' => 'The template for package items.',
    'snippet' => file_get_contents($sources['chunks'].'translexpackageitem.html'),
));

$chunks[7] = $modx->newObject('modChunk');
$chunks[7]->fromArray(array(
    'id' => 7,
    'name' => 'translexTopicContainer',
    'description' => 'The wrapper template for topic items.',
    'snippet' => file_get_contents($sources['chunks'].'translextopiccontainer.html'),
));

return $chunks;