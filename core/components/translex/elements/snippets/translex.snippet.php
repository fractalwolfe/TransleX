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
 * TransleX utility snippet
 *
 * This snippet outputs the entire TransleX utility in the front-end.
 *
 * @package translex
 */
require_once $modx->getOption('translex.core_path',null,$modx->getOption('core_path').'components/translex/').'model/translex/translex.class.php';
$translex = new TransleX($modx,$scriptProperties);
if (!is_object($translex) || !($translex instanceof TransleX)) return '';

$controller = $translex->loadController('FrontEnd');
$output = $controller->run($scriptProperties);
return $output;