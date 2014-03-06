<?php


Class GistList implements ArrayAccess, Iterator {

    protected $baseUrl = 'https://api.github.com';
    protected $username = '';

    protected $gists = array();
    protected $comments = null;
    protected $gistsIdIndex = array();
    protected $currentIndex = 0;

    public function __construct($username) {
        $this->username = $username;
        $this->loadGists();
    }

    protected function loadGists() {
        $url = sprintf('%s/users/%s/gists', $this->baseUrl, $this->username);
        $res = $this->sendApiRequest($url);
        $this->gists = json_decode($res, true);

        foreach($this->gists as $idx => $item) {
            $this->gistsIdIndex[$item['id']] = $idx;           
        }
    }

    protected function sendApiRequest($url) {
        $ch = curl_init(); 
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
        curl_setopt($ch, CURLOPT_USERAGENT, 'ItsMe!');

        $res = curl_exec($ch) ;
        curl_close($ch);

        return $res;
    }

    public function getGists() {
        return $this->gists;
    }

    public function getGist($id) {
        $idx = $this->gistsIdIndex[$id];
        return $this->gists[$idx];
    }




    public function renderHtmlList() {
        $html = sprintf('<h1>%s</h1>', $this->username);
        foreach($this->gists as $gist) {
            $html .= sprintf('<h2>%s</h2><ul>', $gist['description']);
            foreach($gist['files'] as $file) {
                $html .= sprintf('<li><a href="%s">%s</a></li>', $file['raw_url'], $file['filename']);
            }
            $html .= '</ul>';
        }
        return $html;
    }

    public function renderHtmlGist($gist) {
        
        if(is_numeric($gist)) {
            // is gist id?
            $gist = $this->getGist($gist);
        }

        $html = sprintf('<h1>%s</h1>', $gist['description']);
        foreach($gist['files'] as $file) {
            $html .= sprintf('<h2>%s</h2>', $file['filename']);
            $html .= nl2br($this->loadGistFileContent($file['raw_url']));    
        }

        return $html;
    }

    protected function loadGistFileContent($url) {
        return $this->sendApiRequest($url);
    }

    public function renderHtmlComments($gist) {
        
        if(is_numeric($gist)) {
            // is gist id?
            $gist = $this->getGist($gist);
        }

        $this->loadComments($gist['comments_url']);

        $html = sprintf('<h1>%s Comments</h1>', $gist['description']);
        foreach($this->comments as $comment) {
            $html .= sprintf('<li>%s: %s<br />', $comment['user']['login'], $comment['updated_at']);
            $html .= nl2br($comment['body']);
            $html .= '</li>';    
        }

        return $html;
    }

    protected function loadComments($url) {
        if($this->comments !== null) {
            return $this->comments;
        }
        return $this->comments = json_decode($this->sendApiRequest($url), true);
    }


    // array access

    public function offsetSet($offset, $value) {
        if (is_null($offset)) {
            $this->gists[] = $value;
        } else {
            $this->gists[$offset] = $value;
        }
    }
    public function offsetExists($offset) {
        return isset($this->gists[$offset]);
    }
    public function offsetUnset($offset) {
        unset($this->gists[$offset]);
    }
    public function offsetGet($offset) {
        return isset($this->gists[$offset]) ? $this->gists[$offset] : null;
    }


    // foreach iterator

    public function rewind() {
        $this->currentIndex = 0;
    }

    public function current() {
        return $this->gists[$this->currentIndex];
    }

    public function key() {
        return $this->currentIndex;
    }

    public function next() {
        ++$this->currentIndex;
    }

    public function valid() {
        return isset($this->gists[$this->currentIndex]);
    }
}


