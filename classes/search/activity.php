<?php
/**
 * Search area for mod_page activities.
 *
 * @package    mod_etherpadlite
 * @copyright  2016 Silecs {@link www.silecs.info}, Université Paris1-Panthéon-Sorbonne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_etherpadlite\search;

defined('MOODLE_INTERNAL') || die();

require_once dirname(dirname(__DIR__)) . '/etherpad-lite-client.php';

/* @var $DB moodle_database */

/**
 * Search area.
 */
class activity extends \core_search\area\base_activity {
    public function get_recordset_by_timestamp($modifiedfrom = 0) {
        global $DB;

        /*
         * As of Moodle 3.1, the search API requires that this function returns a recordset,
         * which can only be built through a Moodle DB function.
         * All that is really needed is an Iterator. This is bad design.
         * This absurd API leads to querying the DB again when we have the result.
         */
        $records = $DB->get_records('etherpadlite');
        $config = get_config("etherpadlite");
        $client = new \EtherpadLiteClient($config->apikey, $config->url . 'api');

        $recent = [];
        foreach ($records as $r) {
            $lastEdit = $client->getLastEdited($r->uri);
            if (isset($lastEdit->lastEdited) && $lastEdit->lastEdited/1000 > $modifiedfrom) {
                $r->timemodified = (int) ($lastEdit->lastEdited/1000);
                $DB->update_record('etherpadlite', $r);
                /**
                 * @todo Save this modified time through a hourly cron?
                 *   It would render this function useless, but add latency to the search indexing.
                 */
                $recent[] = (int) $r->id;
            }
        }
        if ($recent) {
            return $DB->get_recordset_sql("SELECT * FROM {etherpadlite} WHERE id IN (" . join(',', $recent) . ")");
        } else {
            return $DB->get_recordset_sql("SELECT * FROM {etherpadlite} WHERE 1 = 0");
        }
    }

    /**
     * Returns the document associated with this activity.
     *
     * @param stdClass $record From get_recordset_by_timestamp()
     * @param array    $options
     * @return \core_search\document
     */
    public function get_document($record, $options = []) {
        try {
            $cm = $this->get_cm($this->get_module_name(), $record->id, $record->course);
            $context = \context_module::instance($cm->id);
        } catch (\dml_missing_record_exception $ex) {
            debugging(
                'Error retrieving ' . $this->areaid . ' ' . $record->id . ' document, not all required data is available: '
                . $ex->getMessage(),
                DEBUG_DEVELOPER
            );
            return false;
        } catch (\dml_exception $ex) {
            debugging(
                'Error retrieving ' . $this->areaid . ' ' . $record->id . ' document: ' . $ex->getMessage(),
                DEBUG_DEVELOPER
            );
            return false;
        }

        $config = get_config("etherpadlite");
        $client = new \EtherpadLiteClient($config->apikey, $config->url . 'api');

        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->name, false));
        $doc->set('content', $client->getText($record->uri)->text);
        $doc->set('description1', content_to_text($record->intro, $record->introformat));
        $doc->set('contextid', $context->id);
        $doc->set('type', \core_search\manager::TYPE_TEXT);
        $doc->set('courseid', $record->course);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $record->timemodified);

        if (isset($options['lastindexedtime']) && $options['lastindexedtime'] < $cm->added) {
            $doc->set_is_new(true);
        }

        return $doc;
    }
}
