<div class="gym_tool" id="gym_tool">

    <div class="centered_buttons">
        <input value="gym_exercises_block" type="radio" name="gym_tool_state" id="gym_tool_state1"/><label for="gym_tool_state1">Упражнения</label>
        <input value="gym_programm_block" type="radio" name="gym_tool_state" id="gym_tool_state2"/><label for="gym_tool_state2">Конструктор программ</label>
        <input value="gym_programms_block" type="radio" name="gym_tool_state" id="gym_tool_state3"/><label for="gym_tool_state3">Программы</label>
    </div>

    <!--primary active-->
    <div class="gym_exercises_block">

        {h1}Расположение и название мышц тела человека (не научно){/h1}

        <div class="anatomy">
            <a href="#1" class="trap1">Трапеция</a>
            <a href="#1" class="trap2">Трапеция</a>
            <a href="#3" class="gr">Грудные</a>
            <a href="#4" class="bc">Бицепс</a>
            <a href="#2" class="dl">Дельты</a>
            <a href="#5" class="pr">Предплечья</a>
            <a href="#11" class="kv">Квадрицепсы</a>
            <a href="#13" class="ik">Икры</a>
            <a href="#6" class="tric">Трицепс</a>
            <a href="#12" class="bb">Бицепс бедра</a>
            <a href="#10" class="yg">Ягодичные</a>
            <a href="#8" class="sh">Широчайшие</a>
            <a href="#9" class="rs">Разгибатели<br/>спины</a>
        </div>

        <table class="exercises_control">
            <tr class="view">
                <td class="col1">Вид:</td>
                <td class="col2"></td>
            </tr>
            <tr class="group">
                <td class="col1">Группа мышц:</td>
                <td class="col2">{gymgr_select} <a href="#"><img src="/resources/images/icons/close.png" alt="close" /></a></td>
            </tr>
        </table>

        {gym_exes_table}

        {gym_exes_covers}

        <div class="show_hide_exercises centered_buttons">
            <button>Скрыть упражнения</button>
            <button>Показать все упражнения</button>
        </div>

        {gym_exes_bodies}

    </div>

    <div class="gym_programm_block">

        {gymex_select}

        <div class="user_training_programm">
            <h2 class="name">
                <input type="text" placeholder="Введите название программы"/>{ctrl_button action="clear" name="clear2" title="Очистить форму" hoverable="1"}
            </h2>
            <ol class="user_exercises">
                <li></li>
            </ol>

        </div>

        <div id="gymex_prev">
        </div>

        <div class="prog_ctrl_block">
            <div class="prog_ctrl">
                <button>Сохранить</button>
                <button>Сохранить как новую</button>
                <button>Отменить</button>
            </div>

            <div class="info">
            </div>
        </div>


    </div>





    <div class="gym_programms_block">

        <div class="gym_programms">
        </div>

    </div>

</div>