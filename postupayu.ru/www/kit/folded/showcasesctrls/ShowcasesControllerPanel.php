<?php

/**
 * Панель, храняшая в себе полную информацию о кнопках управления предпросмотрами постов.
 * 
 * Панель собирается один раз на страницу и может быть "спрошен" о том, что будет добавлено.
 * Мы, например, сможем заранее узнать о том, какие js-данные нужно будет добавить, чтобы 
 * передать их в defs при построении страницы и затем к ним можно было обратиться из скрипта
 * фолдинга для плагина.
 *
 * @author azazello
 */
class ShowcasesControllerPanel implements PluggablePanel {

    /** Collection of $ident=>ShowcasesControllerItem */
    private $items;

    function __construct(array $contents) {
        $this->items = $contents;
    }

    /**
     * html - панели с плагинами.
     * Суть довольно проста - самы кнопки переключения виды, это - обычные хрефы.
     * После них мы помещаем невидимый див во всем содержимым, необходимым для работы кнопки переключения:
     * 1. Первоначальный див для предпросмотра постов
     * 2. Собственные кнопочки для данного типа предпросмотра (ShowcasesControllerItem::getPlugins)
     * 
     * Позже, на самой странице, при инициализации плагина всё это будет добавлено и "раскидано" жаваскриптом
     * куда следует (client.js -> PsShowcasesViewController).
     * 
     * Сам плагин управляется из своего .js файла, относящегося к фолдингу данного плагина.
     * Там у него будет весь необходимый API.
     */
    public function getHtml() {
        $params['items'] = $this->items;
        //Решение о том, показывать ли панель, примем на сервере.
        //Если её прятать на клиенте, то на мгновение пользователь всё равно увидит панель.
        //Проверяем empty($this->contents)>0 потому, что list и так будет добавлен вседа
        $params['maincss'] = 'ps-showcases-ctrl-panel' . (empty($this->items) ? ' hidden' : '');
        $params['hintcss'] = 'hint--top hint--info hint--rounded';
        return PSSmarty::template('common/showcases_ctrl_panel.tpl', $params)->fetch();
    }

    public function getJsParams() {
        $result = array();
        /* @var $item ShowcasesControllerItem */
        foreach ($this->items as $ident => $item) {
            $params = $item->getJsParams();
            if (!isTotallyEmpty($params)) {
                $result[$ident] = $params;
            }
        }
        return $result;
    }

    public function getSmartyParams4Resources() {
        $result = array();
        /* @var $item ShowcasesControllerItem */
        foreach ($this->items as $item) {
            $result = array_merge($result, to_array($item->getSmartyParams4Resources()));
        }
        return $result;
    }

}

?>