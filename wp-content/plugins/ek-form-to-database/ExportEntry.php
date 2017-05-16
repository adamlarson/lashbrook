<?php
/*
    "Contact Form to Database" Copyright (C) 2011-2013 Michael Simpson  (email : michael.d.simpson@gmail.com)

    This file is part of Contact Form to Database.

    Contact Form to Database is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Contact Form to Database is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database.
    If not, see <http://www.gnu.org/licenses/>.
*/

require_once('ExportToHtmlTemplate.php');
require_once('CFDBExport.php');
require_once('ExportToHtmlTemplate.php');

class ExportEntry extends ExportToHtmlTemplate implements CFDBExport {

    var $submitTime;

    var $tableId = 'cfdb_entry';

    /**
     * @param $formName string
     * @param $options array of option_name => option_value
     * @return void
     */
    public function export($formName, $options = null) {

        if (!isset($options['submit_time'])) {
            return;
        }
        $this->submitTime = $options['submit_time'];
        unset($options['submit_time']);
        $options['content'] = 'TO BE REPLACED';
        $options['filter'] = 'submit_time=' . $this->submitTime;

        parent::export($formName, $options);
    }

    public function modifyContent($template) {
        $cssUrl = $this->plugin->getPluginDirUrl() . '/css/misctable.css';
        $cssTag = '<link rel="stylesheet" href="' . $cssUrl . '">';

        $javascript = '';
        if ($this->plugin->isEditorActive()) {
            $cfdbEditUrl = admin_url('admin-ajax.php') . '?action=cfdb-edit';
            $cfdbGetValueUrl = admin_url('admin-ajax.php') . '?action=cfdb-getvalue';
            $loadImg = plugins_url('/../contact-form-to-database-extension-edit/img/load.gif', __FILE__);
            $javascript = sprintf(
                    '
<script type="text/javascript">
    jQuery(document).ready(
            function () {
                cfdbEntryEditable("%s", "%s", "%s", "%s");
            });
</script>',
                    $this->tableId, $cfdbEditUrl, $cfdbGetValueUrl, $loadImg);
        }
        
        // EK - Add overrides styling
        $pluginUrl = plugins_url('/', __FILE__);
        $cssTag .= wp_enqueue_style('ekoverride.css', $pluginUrl . 'css/ekoverride.css');

        $template = "{{BEFORE}}$cssTag{{/BEFORE}}" ;
        // EK - Adjusted this to be a bit cleaner and easy to manage the styling (also, no tables)
        $template .= "<div id='{$this->tableId}' class='ek-details'>"; 
        $cols = $this->dataIterator->getDisplayColumns();
        foreach ($cols as $aCol) {
            $colDisplayValue = $aCol;
            if ($this->headers && isset($this->headers[$aCol])) {
                $colDisplayValue = $this->headers[$aCol];
            }
            $template .= sprintf('<div class="ek-details-col-name">%s</div><div id="%s,%s" class="ek-details-col-data">${%s}</div>',
                    $colDisplayValue, $aCol, $this->submitTime, $aCol, $aCol);
        }
        $template .= '</div>';
        $template .= "{{AFTER}}$javascript{{/AFTER}}";
        return $template;
    }

}