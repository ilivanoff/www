<?php

class PostsWatcher {

    /**
     * true - пост ещё не просматривался
     * false - пост уже просматривался
     */
    public static function registerPostWatch(AbstractPost $post) {
        if (!array_key_exists(SESSION_POST_WATCHER_PARAM, $_SESSION)) {
            $_SESSION[SESSION_POST_WATCHER_PARAM] = array();
        }

        $postIdent = IdHelper::ident($post);
        if (in_array($postIdent, $_SESSION[SESSION_POST_WATCHER_PARAM])) {
            return false;
        }

        $_SESSION[SESSION_POST_WATCHER_PARAM][] = $postIdent;

        return true;
    }

}

?>