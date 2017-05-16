<?php
/*
  "Contact Form to Database" Copyright (C) 2011-2012 Michael Simpson  (email : michael.d.simpson@gmail.com)

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

abstract class CFDBView {

    /**
     * @abstract
     * @param  $plugin CF7DBPlugin
     * @return void
     */
    abstract function display(&$plugin);

    protected function pageHeader(&$plugin) {
        $this->sponsorLink($plugin);
        $this->headerLinks($plugin);
    }

    function getRequestParam($name) {
        $value = isset($_REQUEST[$name]) ? $_REQUEST[$name] : '';
        // Prevent javascript injection
        $value = str_ireplace('<script', '', $value);
        return $value;
    }

    /**
     * @param $plugin CF7DBPlugin
     * @return void
     */
    protected function sponsorLink(&$plugin) {
        ?>
        <div style="display: inline-block; float: left;">
            <img class="starting-logo dark-version default-logo" alt="EKR" src="http://ekrcdn.com/ekragency.com/uploads/2015/10/ekr-logo-blue.png" style="height: 54px;">
        </div>
        <?php
    }

    /**
     * @param $plugin CF7DBPlugin
     * @return void
     */
    protected function headerLinks(&$plugin) {
        $notDonated = 'true' != $plugin->getOption('Donated', 'false');
        ?>
        <div style="font-size:x-small; text-align: right; padding: 10px;">
            Based on 
            <a href="http://cfdbplugin.com/" target="_doc" style="text-decoration: none;">
                <img src="<?php echo $plugin->getPluginFileUrl('img/icon-50x50.png') ?>" alt="CFDB"/>
            </a>
            <?php if ($notDonated) { ?>
                <br/>
                <a target="_donate" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=NEVDJ792HKGFN&lc=US&item_name=Wordpress%20Plugin&item_number=cf7%2dto%2ddb%2dextension&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted">
                    Donate to CFDB
                </a> 
            <?php } ?>
        </div>
        <?php
    }

}
