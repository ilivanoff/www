<?php

/**
 * Всё дело в том, что загружать информацию по 10и пользователям отдельно в 10 раз медленнее,
 * чем загрузить информацию по 10и пользователям одним запросом.
 * Данный бин следит за тем, чтобы все пользователи, которые были однажды запрошены,
 * загрузились единовремено пачкой.
 *
 * @author Admin
 */
abstract class UserBatchLoadBean extends BaseBean {

    const LOAD_BATCH_SIZE = 20;

    /**
     * Метод регистрирует пользователя для управления в пачке.
     * Нам не важно - загружены данные по нему уже или нет, мы просто берём его на контроль.
     */
    public final function register($userId, $nullAllowed = false) {
        $userId = AuthManager::validateUserId($userId, $nullAllowed);
        if ($userId === null || $this->CACHE->has($userId)) {
            //Передан null или Пользователь уже добавлен в batch
        } else {
            $this->LOGGER->info('Registering user: ' . $userId);
            $this->CACHE->set($userId, null);
        }
        return $userId;
    }

    /**
     * Метод сбрасывает информацию по пользователю (вызывается после изменения пользователя).
     */
    public final function reset($userId) {
        $userId = AuthManager::validateUserId($userId);
        $this->CACHE->set($userId, null);
        $this->LOGGER->info('Reseting user: ' . $userId);
        return $userId;
    }

    /**
     * Основной метод, который при запросе данных по одному клиенту загрузит их и 
     * по всем остальным.
     * 
     * @return array строка из базы с данными клиента
     */
    public final function getUserDataById($userId) {
        $userId = $this->register($userId);
        if ($this->CACHE->isArray($userId)) {
            return $this->CACHE->get($userId);
        }

        $this->LOGGER->info('Loading user: ' . $userId);

        /*
         * Соберём пачку для загрузки
         */
        $batch = array();
        foreach ($this->CACHE->keys() as $userId) {
            if ($this->CACHE->isArray($userId)) {
                continue;
            }
            if (count($batch) >= self::LOAD_BATCH_SIZE) {
                $this->preloadUsersByIds($batch);
                $batch = array();
            }
            $batch[] = $userId;
        }
        $this->preloadUsersByIds($batch);

        return $this->CACHE->get($userId);
    }

    /**
     * Метод возвращает свойство пользователя из БД
     */
    public final function getUserProperty($userId, $property) {
        $row = $this->getUserDataById($userId);
        check_condition(array_key_exists($property, $row), "Свойство '$property' недопустимо для пользователя");
        return $row[$property];
    }

    /**
     * Метод загружает пользователей. Если пользователь зарегистрирован,
     * но не найден - ругаемся. Не должно быть мест в системе, в которых мы обращаемся
     * к несуществующему пользователю!
     */
    private function preloadUsersByIds(array $userIds) {
        $count = count($userIds);
        switch ($count) {
            case 0:
                break; //----
            case 1:
                $this->LOGGER->info('Selecting single user: ' . $userIds[0]);
                $this->registerLoaded($userIds[0], $this->getRec('select * from users where id_user=?', $userIds[0]));
                break; //----
            default:
                sort($userIds);
                if ($this->LOGGER->isEnabled()) {
                    $this->LOGGER->info('Selecting batch users: ' . array_to_string($userIds));
                }
                $users = $this->getArrayIndexed('select * from users where id_user in (' . implode(', ', array_fill(0, $count, '?')) . ')', $userIds, 'id_user');
                foreach ($userIds as $userId) {
                    $this->registerLoaded($userId, array_get_value($userId, $users));
                }
                break; //---
        }
    }

    /**
     * Метод регистрирует загруженного пользователя.
     * Если данных по пользователю не вернулось, значит его не существует и мы будем ругаться!
     */
    private function registerLoaded($userId, array $row = null) {
        check_condition(is_array($row), "Пользователь с кодом $userId не существует");
        $this->CACHE->set($userId, $row);
    }

}

?>