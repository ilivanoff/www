<?php

/**
 * Менеджер выпуска журналов
 */
class MagManager extends PostsProcessor {

    const IDENT_PREFIX = 'Issue';

    public function newsTitle() {
        return 'Вышел выпуск журнала';
    }

    public function postsTitle() {
        return 'Выпуски журнала';
    }

    protected function postTitleImpl() {
        return 'Выпуск журнала';
    }

    public function coverDims() {
        return '200x';
    }

    public function getPostsListPage() {
        return WebPage::inst(BASE_PAGE_MAGAZINE);
    }

    public function getPostPage() {
        return WebPage::inst(PAGE_ISSUE);
    }

    public function dbBean() {
        return ISBean::inst();
    }

    public function getPostType() {
        return POST_TYPE_ISSUE;
    }

    /**
     * Возвращает последний фолдинг для журнала. Если фолдингов вообще нет - вернётся null.
     */
    private function getLastFoldingIdent() {
        $foldings = $this->getFolding()->getAllIdents();
        usort($foldings, function($f1, $f2) {
                    return MagManager::ident2id($f1) > MagManager::ident2id($f2) ? 1 : -1;
                });
        return count($foldings) == 0 ? null : end($foldings);
    }

    /**
     * Возвращает идентификатор следующего фолдинга для журнала.
     */
    public function getNextFoldingIdent() {
        $last = $this->getLastFoldingIdent();
        $id = $last ? self::ident2id($last) + 1 : 1;
        return self::IDENT_PREFIX . $id;
    }

    /**
     * Заменим название для выпуска журанла
     */
    protected function addVirtualPostParams($ident, array $cols4replace) {
        $replaceName = !array_key_exists('name', $cols4replace);
        $cols4replace = parent::addVirtualPostParams($ident, $cols4replace);
        if ($replaceName) {
            //Если имя не передано извне, заменим его на 'Выпуск №N'
            $cols4replace['name'] = 'Выпуск №' . self::ident2id($ident);
        }
        return $cols4replace;
    }

    /**
     * Функция возвращает код журнала по его id
     */
    public static function ident2id($ident) {
        check_condition(starts_with($ident, self::IDENT_PREFIX), "Bad issue ident: [$ident]");
        $postId = cut_string_start($ident, self::IDENT_PREFIX);
        check_condition(is_numeric($postId), "Bad issue ident: [$ident]");
        return 1 * $postId;
    }

    /** @return MagManager */
    public static function inst() {
        return parent::inst();
    }

}

?>