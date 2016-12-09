<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Code fragment to define the version of etherpadlite
 * This fragment is called by moodle_needs_upgrading() and /admin/index.php
 *
 * @package    mod_etheradlite
 *
 * @author     Timo Welde <tjwelde@gmail.com>
 * @author     François Gannaz <francois.gannaz@silecs.info>
 * @copyright  2012 Humboldt-Universität zu Berlin <moodle-support@cms.hu-berlin.de>
 * @copyright  2016 Université Paris 1 Panthéon-Sorbonne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->version    = 2016120901;  // The current module version (Date: YYYYMMDDXX)
$plugin->requires   = 2014051200;
$plugin->cron       = 0;           // Period for cron to check this module (secs)
$plugin->component  = 'mod_etherpadlite';
$plugin->maturity   = MATURITY_STABLE;
$plugin->release    = '2.8.0 (build 2016120901)';
