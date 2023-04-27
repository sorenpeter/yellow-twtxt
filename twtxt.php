<?php
// Twtxt extension, https://github.com/sorenpeter/yellow-twtxt

// Include PicoBlog
include_once $this->yellow->system->get("coreServerBase").'system/extensions/twtxt-picoblog.php'; // TODO: This can be done nicer


class YellowTwtxt {
    const VERSION = "0.0.1";
    public $yellow;         // access to API
    
    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("twtxtLocation", "twtxt.txt");
    }


    // Handle page content of shortcut
    public function onParseContentShortcut($page, $name, $text, $type) {
        $output = null;

// PicoBlog: Instantiate the class with the source file
$mb = new \hxii\PicoBlog(htmlspecialchars($this->yellow->system->get("twtxtLocation")));

// PicoBlog: Parse query string and get blog entries
$query = $mb->parseQuery();
//$entries = ($query) ? $mb->getEntries($query) : $mb->getEntries('all');
$entries = ($query) ? $mb->getEntries($query) : $mb->getEntries('all', true); // sort in reverse is now true

        if ($name=="twtxt" && ($type=="block" || $type=="inline")) {
            $location = $this->yellow->system->get("twtxtLocation");
            if (substru($text, 0, 2)=="- ") $message = trim(substru($text, 2));
                // Display message and link to main list if viewing a filtered entry list
                if ($query) {
                    $filterType = preg_replace("/=.*$/", "", $_SERVER['QUERY_STRING']);
                    $output = '<p class="twtxt-filter"><a href="'.strtok($_SERVER["REQUEST_URI"], '?').'">Go back to full timeline</a>';
                        $output .= '&nbsp;&ndash;&nbsp;';
                        //$output .= '<br>';
                        if ($filterType == "id") {
                            $datetime = implode('', $query);
                            $datetime = rtrim($datetime, "Z");
                            $datetime = str_replace("T"," ",$datetime);
                            $output .= 'Currently viewing post from: <b>'.$datetime.'</b>';
                        }
                        elseif ($filterType == "tag") {
                            $output .= 'Currently viewing posts tagged with: <b>#'.implode('', $query).'</b>';
                        }
                    $output .= '</p>';
                }

            $output .= "<ul class=\"twtxt\">".$mb->renderEntries($entries, "<li class=\"twt\">{entry}</li>")."</ul>";
        }
        return $output;
    }
    
    // Handle page extra data
    public function onParsePageExtra($page, $name) {
        $output = null;
        if ($name=="header") {
            $extensionLocation = $this->yellow->system->get("coreServerBase").$this->yellow->system->get("coreExtensionLocation");
            $output = "<link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"{$extensionLocation}twtxt.css\" />\n";
            // $output .= "<script type=\"text/javascript\" defer=\"defer\" src=\"{$extensionLocation}helloworld.js\"></script>\n";
        }
        return $output;
    }
}
