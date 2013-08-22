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
 * TransleX build-script config
 *
 * @package translex
 * @subpackage build
 */
/* Define package details */
define('PKG_NAME','TransleX');
define('PKG_NAMESPACE',strtolower(PKG_NAME));
define('PKG_VERSION','2.0.0');
define('PKG_RELEASE','rc1');

if (!defined('MODX_CORE_PATH')) {
    define('MODX_CORE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/core/');
    define('MODX_BASE_PATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
    define('MODX_MANAGER_PATH', MODX_BASE_PATH . 'manager/');
    define('MODX_CONNECTORS_PATH', MODX_BASE_PATH . 'connectors/');
    define('MODX_ASSETS_PATH', MODX_BASE_PATH . 'assets/');
}

/* not used -- here to prevent E_NOTICE warnings */
if (!defined('MODX_BASE_URL')) {
    define('MODX_BASE_URL', '/');
    define('MODX_MANAGER_URL', MODX_BASE_URL . 'manager/');
    define('MODX_CONNECTORS_URL', MODX_BASE_URL . 'connectors/');
    define('MODX_ASSETS_URL', MODX_BASE_URL . 'assets/');
}

