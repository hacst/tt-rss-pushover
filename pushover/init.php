<?php
class Pushover extends Plugin {
    private $host;

    function about() {
        return array(1.0,
            "Forwards articles to pushover",
            "hacst");
    }

    function flags() {
        return array("needs_curl" => true);
    }

    function init($host) {
        $this->host = $host;
        $host->add_filter_action($this, "action_pushover", __("Notify via pushover"));
    }

    function hook_article_filter_action($article, $action) {
        // Pushover limits length of title and content. Content must not be empty.
        $title = mb_strimwidth($article["title"], 0, 250, "...", "UTF-8");
        $content = mb_strimwidth($article["content"], 0, 1024, "...", "UTF-8");
        if ($content == "") $content = "<div></div>";

        curl_setopt_array($ch = curl_init(), array(
            CURLOPT_URL => "https://api.pushover.net/1/messages.json",
            CURLOPT_POSTFIELDS => array(
                "token" => "",
                "user" => "",
                "title" => $title,
                "message" => $content,
                "url" => $article["link"],
                "html" => 1
            ),
            CURLOPT_SAFE_UPLOAD => true,
            ));
        curl_exec($ch);
        curl_close($ch);

        return $article;
    }

    function api_version() {
        return 2;
    }
}
?>
