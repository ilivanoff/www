<?php

/**
 * Вспомогательный класс для работы не с конкретным пользователем, а со всеми 
 * пользователями сразу.
 * 
 * Класс в основном перенаправляет реальные вызовы на пользователя или выполняет действия,
 * вызывая статические методы других классов, поэтому сделаем его статическим.
 *
 * @author azazello
 */
final class PsUserHelper {

    /**
     * Метод вовзращает ссылку на DirItem аватара.
     * Если задан пользователь, то будет возвращён его аватар, иначе - anonymous
     * 
     * @return DirItem
     */
    public static function getAvatarDi(PsUser $user = null, $dim = null) {
        return $user ? $user->getAvatarDi($dim) : self::getDefaultAvatarDi(null, $dim);
    }

    /**
     * Метод вовзращает ссылку на DirItem аватара по умолчанию.
     * Если задан пользователь, то будет возвращён его аватар по умолчанию, иначе - anonymous.
     * 
     * @return DirItem
     */
    public static function getDefaultAvatarDi(PsUser $user = null, $dim = null) {
        return $user ? $user->getDefaultAvatarDi($dim) : PsImgEditor::resizeBase('anonymous.jpg', $dim);
    }

    /**
     * Метод возвращает <img /> элемент, готовый для вставки на страницу
     */
    public static function getAvatarImg(PsUser $user = null, $dim = false, array $params = array()) {
        $params['src'] = self::getAvatarDi($user, $dim);
        $params['alt'] = $user ? $user->getName() : 'Аноним';
        $params['data'] = $user ? array('uid' => $user->getId()) : null;

        $params['class'] = to_array(array_get_value('class', $params));
        $params['class'][] = array('avatar', $user ? 'user' : null);
        return PsHtml::img($params);
    }

}

?>