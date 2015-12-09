<?php

/**
 * Класс получает на вход: 
 * 1. Ассоциативный массив ячеек с параметрами: x_cell, y_cell, id_user
 * 2. Размеры ячеек w и h
 * 3. Признак - проверять ли принадлежность ячейки к пользователю
 * На выходе получается массив ячеек с параметрами: x1, y1, x2, y2, id_user
 * 
 * Алгоритм работы достаточно прост - идём от левого верхнего угла картинки по всем ячейкам, для каждой ячейки:
 * 1. Проверяем, можно ли объединиться с ячейкой справа, если можно - объединяемся
 * 2. Проверяем, можно ли объединиться с ячейкой снизу, если можно - объединяемся
 * 
 * Отметим, что x_cell и y_cell - это ячейки, нумерация которых начинается с 1, в результирующием наборе
 * координаты будут начинаться с 0.
 */
class MosaicImageCellsCompositor {

    private $w;
    private $h;
    private $cells;
    private $result;
    private $checkUser;

    /** @var PsLoggerInterface */
    private $LOGGER;

    private function __construct(array $cells, $w, $h, $checkUser) {
        $this->LOGGER = PsLogger::inst(__CLASS__);

        $this->w = $w;
        $this->h = $h;
        $this->checkUser = !!$checkUser;
        $this->result = array();
        $this->doProcess($cells);
    }

    /**
     * Основной метод, выполняющий всю работу. Для каждой мозайки будет создан отдельный объект, который и выполнит объединение ячеек.
     * 
     * @param array $cells - ячейки (x_cell, y_cell, id_user)
     * @param type $w - ширина ячейки
     * @param type $h - высота ячейки
     * @return array - обработанный массив
     */
    public static function union(array $cells, $w, $h, $checkUser) {
        if (empty($cells)) {
            return array();
        }
        $composer = new MosaicImageCellsCompositor($cells, $w, $h, $checkUser);
        return $composer->result;
    }

    /**
     * Метод объединяет ячейки и строит набор облостей <area> для разметки картинки
     */
    public static function area(array $cells, $w, $h) {
        $result = '';
        foreach (self::union($cells, $w, $h, true) as $cell) {
            $x1 = $cell[0];
            $y1 = $cell[1];
            $x2 = $cell[2];
            $y2 = $cell[3];
            $id_user = $cell[4];
            $result .= "<area nohref=\"nohref\" SHAPE=\"rect\" coords=\"$x1, $y1, $x2, $y2\" data-id=\"$id_user\"/>";
        }
        return $result;
    }

    /**
     * Проверяет, можно ли объединиться с ячейкой. Это возможно, если:
     * 1. Ячейка существует
     * 2. Мы не проверяем принадлежность ячейки пользователю либо ячейка принадлежит этому пользователю
     */
    private function isCanUnionWith($x, $y, $id_user) {
        $ident = $x . 'x' . $y;
        $cell = array_get_value($ident, $this->cells);
        return is_array($cell) && (!$this->checkUser || ($cell['id_user'] == $id_user));
    }

    /**
     * Кол-во ячеек справа, с которыми можно объединиться.
     */
    private function getRightCellsCnt($x, $y, $id_user) {
        $cnt = 0;
        while ($this->isCanUnionWith($x + ++$cnt, $y, $id_user)) {
            //
        }
        return--$cnt;
    }

    /**
     * Кол-во ячеек снизу, с которыми можно объединиться.
     * С ячейкой снизу можно объединяться только если она не может объединиться с ячейкой по горизонтали слева или справа.
     */
    private function getBottomCellsCnt($x, $y, $id_user) {
        $cnt = 0;
        while ($this->isCanUnionWith($x, $y + ++$cnt, $id_user)) {
            if ($this->isCanUnionWith($x - 1, $y + $cnt, $id_user) || $this->isCanUnionWith($x + 1, $y + $cnt, $id_user)) {
                break;
            }
        }
        return--$cnt;
    }

    /**
     * Метод, выполняющий всё вычисление и всю обработку
     */
    private function doProcess($cells) {
        PsProfiler::inst(__CLASS__)->start('Unioning cells');

        if ($this->LOGGER->isEnabled()) {
            $this->LOGGER->infoBox('Unioning ' . count($cells) . ' cells, user check: ' . var_export($this->checkUser, true));
            $this->LOGGER->info();
            $this->LOGGER->info('Before sorting and indexing:');
            $this->LOGGER->info(print_r($cells, true));
        }

        //Расположим ячейки от левого верхнего угла до правого нижнего (как при письме)
        usort($cells, function($c1, $c2) {
                    if ($c1['y_cell'] == $c2['y_cell']) {
                        return $c1['x_cell'] > $c2['x_cell'] ? 1 : -1;
                    }
                    return $c1['y_cell'] > $c2['y_cell'] ? 1 : -1;
                });


        $this->cells = array();

        //Проиндексируем массив
        foreach ($cells as $cell) {
            $cell['x_cell'] = 1 * $cell['x_cell'];
            $cell['y_cell'] = 1 * $cell['y_cell'];
            $cell['id_user'] = 1 * $cell['id_user'];
            $this->cells[$cell['x_cell'] . 'x' . $cell['y_cell']] = $cell;
        }

        if ($this->LOGGER->isEnabled()) {
            $this->LOGGER->info();
            $this->LOGGER->info('After sorting and indexing:');
            $this->LOGGER->info(print_r($this->cells, true));
            $this->LOGGER->info();
        }

        //Пробегаем по всем ячейкам, объединяя их и исключая те ячейки, с которыми мы объединились
        $unioned = 0;
        foreach ($this->cells as $ident => $cell) {
            if (!array_key_exists($ident, $this->cells)) {
                //Видимо уже удаили ячейку из списка ячеек
                continue;
            }

            $x = $cell['x_cell'];
            $y = $cell['y_cell'];
            $id_user = $cell['id_user'];

            $dx = $this->getRightCellsCnt($x, $y, $id_user);
            if ($dx > 0) {
                //Объединяемся с ячейками справа
                $x1 = ($x - 1) * $this->w;
                $y1 = ($y - 1) * $this->h;
                $x2 = ($x + $dx) * $this->w;
                $y2 = $y * $this->h;

                for ($delta = 1; $delta <= $dx; $delta++) {
                    $cident = ($x + $delta) . 'x' . $y;
                    if ($this->LOGGER->isEnabled()) {
                        $cid_user = array_get_value_in(array($cident, 'id_user'), $this->cells);
                        $this->LOGGER->info(++$unioned . ". [$ident]($id_user) + [$cident]($cid_user)");
                    }
                    unset($this->cells[$cident]);
                }
                $this->result[] = array($x1, $y1, $x2, $y2, $id_user);

                continue;
            }

            $dy = $this->getBottomCellsCnt($x, $y, $id_user);
            if ($dy > 0) {
                //Объединяемся с ячейками снизу
                $x1 = ($x - 1) * $this->w;
                $y1 = ($y - 1) * $this->h;
                $x2 = $x * $this->w;
                $y2 = ($y + $dy) * $this->h;

                for ($delta = 1; $delta <= $dy; $delta++) {
                    $cident = $x . 'x' . ($y + $delta);
                    if ($this->LOGGER->isEnabled()) {
                        $cid_user = array_get_value_in(array($cident, 'id_user'), $this->cells);
                        $this->LOGGER->info(++$unioned . ". [$ident]($id_user) + [$cident]($cid_user)");
                    }
                    unset($this->cells[$cident]);
                }
                $this->result[] = array($x1, $y1, $x2, $y2, $id_user);

                continue;
            }

            //Не с кем объединяться, берём только эту ячейку
            $x1 = ($x - 1) * $this->w;
            $y1 = ($y - 1) * $this->h;
            $x2 = $x * $this->w;
            $y2 = $y * $this->h;
            $this->result[] = array($x1, $y1, $x2, $y2, $id_user);
        }
        $secundomer = PsProfiler::inst(__CLASS__)->stop();

        if ($this->LOGGER->isEnabled()) {
            $this->LOGGER->info();
            $this->LOGGER->info('Unioned cells:');
            $this->LOGGER->info(print_r($this->result, true));
            $this->LOGGER->info('Compression: {}%', round((count($this->result) / count($cells)) * 100));
            if ($secundomer) {
                $this->LOGGER->info('Done in ' . $secundomer->getAverage() . ' seconds');
            }
        }
    }

}

?>