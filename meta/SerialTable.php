<?php

namespace dokuwiki\plugin\struct\meta;

/**
 * Class SerialTable
 *
 * An AggregationTable for editing serial tables
 *
 * @package dokuwiki\plugin\struct\meta
 */
class SerialTable extends AggregationTable {

    /**
     * @var bool skip full table when no results found
     */
    protected $simplenone = false;

    /**
     * Adds additional info to document and renderer in XHTML mode
     *
     * We add the schema name as data attribute
     *
     * @see finishScope()
     */
    protected function startScope() {
        // unique identifier for this aggregation
        $this->renderer->info['struct_table_hash'] = md5(var_export($this->data, true));

        if($this->mode != 'xhtml') return;

        $table = $this->columns[0]->getTable();

        $config = $this->searchConfig->getConf();
        if(isset($config['filter'])) unset($config['filter']);
        $config = hsc(json_encode($config));

        // wrapping div
        $this->renderer->doc .= "<div class=\"structaggregation structserial\" data-schema=\"$table\" data-searchconf=\"$config\">";

        // unique identifier for this aggregation
        $this->renderer->info['struct_table_hash'] = md5(var_export($this->data, true));
    }

    /**
     * We do not output a row for empty tables
     */
    protected function renderEmptyResult() {
    }

    /**
     * Renders the first result row and returns it
     *
     * Only used for rendering new rows via JS (where the first row is the only one)
     *
     * @return string
     */
    public function getFirstRow() {
        // XHTML renderer doesn't like calling ->tablerow_open() without
        // ->table_open() first, since it leaves some internal variables unset.
        // Therefore, call ->table_open() and throw away the generated HTML.
        $this->renderer->table_open();
        $this->renderer->doc = '';

        $latestRid = max($this->resultRids); // 16
        $key = array_search($latestRid, $this->resultRids);
        $this->renderResultRow($key, $this->result[$key]);
        return $this->renderer->doc;
    }

}