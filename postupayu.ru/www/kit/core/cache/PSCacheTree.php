<?php

/**
 * Класс для работы с деревом зависимости групп кешей от сущностей системы.
 * 
 * Всё строится на дереве зависимостей:
 * Тип дочерней сущности -> Группа кеширования -> Дочерняя сущность
 * 
 * Эта зависимость задаётся на уровне маппингов и влияет на валидность групп кеширования.
 * 
 * В момент инициализации PSCache, мы строим эту карту и передаём её в данный класс, который
 * клонирует её в два массива и с помощью них отслеживает свежесть групп кешей. Пример карты:

  +++++++++++++++++++++++++++++
  + Начальное состояние групп +
  +++++++++++++++++++++++++++++
  |NEWS:
  |	Фолдинги:
  |		post-is
  |		rubric-bp
  |		post-bp
  |		rubric-tr
  |		post-tr

  |POSTS:
  |	Фолдинги:
  |		post-is
  |		rubric-bp
  |		post-bp
  |		rubric-tr
  |		post-tr

  |POPUPS:
  |	Фолдинги:
  |		post-is
  |		rubric-bp
  |		post-bp
  |		rubric-tr
  |		post-tr
  |		pl
  |		pp

  |GALLERY:
  |	Сущности базы:
  |		ps_gallery
  |		ps_gallery_images

  |TIMELINES:
  |	Сущности базы:
  |		ps_lib_item
  |		v_ps_lib_item
  |		ps_timeline
  |		ps_timeline_item

 * 
 * Этот класс вызывается из PSCache и сам к PSCache не обращается.
 * 
 * Различаются две ситуации:
 * 1. Валидация группы кеширования
 * 2. Оповещение об изменении дочерней сущности
 * 
 * Валидация производится при обращении к группе кеширования и подразумевает,
 * что будут проверены все сущности, от которых зависит группа кеширования. Лишь 
 * после этого можно будет к группе обратиться.
 * 
 * Тем не менее даже после прохождения валидации, если поступит событие об изменении
 * дочерней сущности, група будет очищена. При этом повторное событие об изменении той-же 
 * сущности будет проигнорировано.
 * 
 * В данном классе имеются все методы, позволяющие отмечать любой их элементов в дереве
 * зависимостей как провалидированный.
 * 
 * Естесственно методы автоматически отслеживают, чтобы поступающие события для любой дочерней 
 * сущности транслировались на все ветки дерева зависимостей, где есть эта же сущность.
 * 
 * Важно понимать, что условием отвязки (удаления из дерева) зависимости группы кеширования от 
 * дочерней сущности является изменение этой дочерней сущности! Валидация лишь позволяет повторно
 * не проверять дочерние элементиы на изменение.
 * 
 * @author azazello
 */
class PSCacheTree {

    /** @var PsLoggerInterface */
    private $LOGGER;

    /** @var array Дерево зависимостей */
    private $TREE_DEP;

    /** @var array Дерево валидации */
    private $TREE_VAL;

    public function __construct($LOGGER, $TREE) {
        $this->LOGGER = $LOGGER;
        $this->TREE_DEP = $TREE;
        $this->TREE_VAL = $TREE;

        $this->logTrees('Начальное состояние групп');
    }

    /**
     * Метод просматривает дерево и возвращает группы, зависящие от указанного дочернего элемента.
     * 
     * Если после удаления этого дочернего элемента группа больше не зависит ни от одного элемента
     * этого типа, то данная группа будет исключена из ветки зависимости для этого типа.
     * 
     * $cleanAffectedGroups - данный флаг позволяет сказать, что группа должна быть сразу очищена
     * при обнаружении, что она зависит от переданной дочерней сущности. Это нужно для того, чтобы 
     * отметить всю группу - провалидированной в случае, если дочерняя сущность была изменена.
     */
    private function scanTreeAndGetAffectedGroups($type, $child, &$TREE, $cleanAffectedGroups = false) {
        $affectedGroups = array();

        if (array_key_exists($type, $TREE)) {
            foreach ($TREE[$type] as $group => &$childEntitys) {
                if (in_array($child, $childEntitys)) {
                    $affectedGroups[] = $group;
                    if ($cleanAffectedGroups) {
                        unset($TREE[$type][$group]);
                    } else {
                        array_remove_value($childEntitys, $child);
                        if (empty($childEntitys)) {
                            unset($TREE[$type][$group]);
                        }
                    }
                    if (empty($TREE[$type])) {
                        unset($TREE[$type]);
                    }
                }
            }
        }

        return array_unique($affectedGroups);
    }

    /**
     * Функция вызывается после прохождения валидации дочерней сущностью.
     * Наличие этой дочки будет проверено и в других ветках.
     */
    public function onChildValidated($type, $child) {
        $affected = $this->scanTreeAndGetAffectedGroups($type, $child, $this->TREE_VAL);
        $this->logTrees("Провалидировано '$type' [$child]. Повлияло на: " . array_to_string($affected), !empty($affected));
    }

    /**
     * Функция вызывается при изменении дочерней сущности.
     * Если мы уже были оповещены один раз об изменении сущности, то больше на
     * неё реагировать не будем.
     */
    public function onChildChanged($type, $child) {
        $this->scanTreeAndGetAffectedGroups($type, $child, $this->TREE_VAL, true);
        $affected = $this->scanTreeAndGetAffectedGroups($type, $child, $this->TREE_DEP);
        $this->logTrees("Изменено '$type' [$child]. Повлияло на: " . array_to_string($affected), !empty($affected));
        return $affected;
    }

    /**
     * Метод проверяет, есть ли группы кеширования, зависящие от данного типа дочерних сущностей.
     */
    public function isTypeValidateble($type) {
        return array_key_exists($type, $this->TREE_VAL);
    }

    /**
     * Функция вызывается после валидации всех групп, входящих в данный тип,
     * или после выполнения действия, гарантирующего валидность всех сущностей, входящих в этот тип -
     * например проверка изменённых сущностей в БД.
     */
    public function setTypeValidated($type, $reason = null) {
        if ($this->isTypeValidateble($type)) {
            unset($this->TREE_VAL[$type]);
            $this->logTrees("Зависимость от [$type] полностью провалидирована, причина: " . $reason, !!$reason);
        }
    }

    /**
     * Функция вызывается для отметки о валидности всех групп.
     */
    public function setAllValidated($reason) {
        if (!empty($this->TREE_VAL)) {
            $this->TREE_VAL = array();
            $this->logTrees('Всё провалидировано, причина: ' . $reason);
        }
    }

    /**
     * Проверяет, должна ли данная группа быть провалидирована.
     */
    public function isGroupValidatable($group) {
        if (!in_array($group, PSCache::getCacheGroups())) {
            return false; //---
        }
        foreach ($this->TREE_VAL as $groups2childs) {
            if (array_key_exists($group, $groups2childs)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Отмечает группу, как провалидированную.
     */
    public function setGroupValidated($group) {
        if (!$this->isGroupValidatable($group)) {
            return; //---
        }
        foreach ($this->TREE_VAL as $type => &$groups2childs) {
            if (array_key_exists($group, $groups2childs)) {
                unset($groups2childs[$group]);
                if (empty($groups2childs)) {
                    $this->setTypeValidated($type);
                }
            }
        }
        $this->logTrees("Группа [$group] отмечена, как провалидированная");
    }

    /**
     * Возвращает дочерние сущности заданного типа, от которых зависит данная группа кеширования.
     */
    public function getChildsForValidate($type, $group) {
        return to_array(array_get_value_in(array($type, $group), $this->TREE_VAL));
    }

    /**
     * Распечатывает текущее состояние дерева зависимости в лог.
     * При этом в дереве отмечаются всегда только полностью проверенные сущности.
     * Если, например, группа зависит от таблиц БД, которые были провалидированы, но при этом
     * сама группа отмечена, как валидная, то мы увидим подобную картину:
     * 
      +GALLERY:
      |	Сущности базы:
      |		ps_gallery
      |		ps_gallery_images
     */
    private function logTrees($caption, $doLog = true) {
        if (!$doLog || !$this->LOGGER->isEnabled()) {
            return; //---
        }
        $this->LOGGER->infoBox($caption);

        $GROUPS = PSCache::getCacheGroups();

        $hasPrintedGroups = false;
        foreach ($GROUPS as $group) {
            $isGroupPrinted = false;
            foreach ($this->TREE_DEP as $type => $groups2entitys) {
                if (!array_key_exists($group, $groups2entitys)) {
                    continue;
                }

                $valGr = !$this->isGroupValidatable($group);
                $valType = !$valGr && !$this->isTypeValidateble($type);

                if ($hasPrintedGroups && !$isGroupPrinted) {
                    $this->LOGGER->info();
                }

                if (!$isGroupPrinted) {
                    $isGroupPrinted = true;
                    $hasPrintedGroups = true;
                    $this->LOGGER->info("\t" . ($valGr ? '+' : '|') . $group . ':');
                } else {
                    $this->LOGGER->info("\t|");
                }
                $this->LOGGER->info("\t" . ($valType ? '+' : '|' ) . "\t$type:");
                foreach ($groups2entitys[$group] as $child) {
                    $varChild = !$valGr && !$valType && !in_array($child, $this->getChildsForValidate($type, $group));
                    $this->LOGGER->info("\t" . ($varChild ? '+' : '|') . "\t\t$child");
                }
            }
        }
        $this->LOGGER->info(pad_left('', ps_strlen($caption) + 4, '+'));
        $this->LOGGER->info();
    }

}

?>