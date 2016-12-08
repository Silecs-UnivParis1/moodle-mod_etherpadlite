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

/**
 * Search area.
 */
class activity extends \core_search\area\base_activity {
    /**
     * Returns the document associated with this activity.
     *
     * @param stdClass $record Moodle does not document that, please guess the fields.
     * @param array    $options
     * @return \core_search\document
     */
    public function get_document($record, $options = []) {
        global $DB;

        try {
            $context = \context_course::instance($record->contextid);
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

        $etherpadlite = $DB->get_record('etherpadlite',  ['id' => $record->instance], '*', MUST_EXIST);
        $config = get_config("etherpadlite");
        $client = new EtherpadLiteClient($config->apikey, $config->url . 'api');

        $doc = \core_search\document_factory::instance($record->id, $this->componentname, $this->areaname);
        $doc->set('title', content_to_text($record->name, false));
        $doc->set('content', $client->getText($etherpadlite->uri));
        $doc->set('contextid', $context->id);
        $doc->set('type', \core_search\manager::TYPE_TEXT);
        $doc->set('courseid', $record->course);
        $doc->set('owneruserid', \core_search\manager::NO_OWNER_ID);
        $doc->set('modified', $record->timemodified);
        $doc->set('description1', content_to_text($record->intro, $record->introformat));

        if (isset($options['lastindexedtime']) && $options['lastindexedtime'] < $record->added) {
            $doc->set_is_new(true);
        }

        return $doc;
    }
}
