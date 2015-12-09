<?php

/**
 * Бин для работы с расширениями слов
 * 
 * Данный бин должен уметь работать автономно, так как служит, по большому счёту, просто кешем
 * 
 * @author azazello
 */
final class InflectsBean extends BaseBean {

    public function getInflections($word) {
        $inflections = $this->isConnected() ? $this->getRec('select v_word,v_var1,v_var2,v_var3,v_var4,v_var5,v_var6 from ps_inflects where v_word COLLATE utf8_bin =?', $word) : null;
        return is_array($inflections) ? array_values($inflections) : null;
    }

    public function saveInflections(array $inflections) {
        if ($this->isConnected()) {
            if ($this->getCnt('select count(1) as cnt from ps_inflects where v_word COLLATE utf8_bin =?', $inflections[0]) == 0) {
                $this->insert('insert into ps_inflects (v_word, v_var1, v_var2, v_var3, v_var4, v_var5, v_var6) VALUES (?, ?, ?, ?, ?, ?, ?)', $inflections);
            }
        }
    }

}

?>