<?php

function smarty_block_part($params, $content, Smarty_Internal_Template & $smarty) {
    if (!$content) {
        return;
    }

    $post = PostFetchingContext::getInstance()->getPost();
    $postType = $post->getPostType();

    $content = trim($content);
    $descr = value_Array(array('desc', 'descr'), $params);

    switch ($postType) {
        case POST_TYPE_ISSUE:
            //ЖУРНАЛ
            if ($content == 'EDITOR') {
                $postId = MagManager::ident2id($post->getIdent());
                echo "<h4 class=\"ps-post-head section\"><span>Выпуск $postId.</span> Редакторская колонка</h4>";
            } else {
                switch ($content) {
                    case 'WHOIS':
                        $content = 'Кто это?';
                        break;
                    case 'TASKS':
                        PostFetchingContext::getInstance()->resetTasksNumber();
                        $content = 'Задачки на подумать';
                        break;
                    case 'TASKS_A':
                        $content = 'Ответы на задачи предыдущего номера';
                        break;
                    case 'CITATA':
                        $content = 'Цитата номера';
                        break;
                    case 'VERSE':
                        $content = "Стихи, $descr";
                        break;
                    case 'HUMOR':
                        $content = 'Студенческий юмор';
                        break;
                }

                $curNum = PostFetchingContext::getInstance()->addAnons($content);
                echo "<h4 id=\"p$curNum\" class=\"section\"><span>Часть#$curNum.</span> $content</h4>";
            }

            break;

        case POST_TYPE_TRAINING:
            //УПРАЖНЕНИЯ
            $curNum = PostFetchingContext::getInstance()->addAnons($content);
            echo "<h5 id=\"p$curNum\" class=\"paragraph\">$curNum. $content</h5>";
            break;

        default:
            check_condition(false, __FUNCTION__ . ' function cannot be used with post type ' . $postType);
    }
}

?>