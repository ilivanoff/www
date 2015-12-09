<div class="advgraph_plugin">

    {dekart}

    <div class="grafics">
        <div class="hg-next">
            Режим: 
            <a href="#">не выбран</a>
            <a href="#der">касательная</a>
            <a href="#int">интерграл</a>
        </div>

        <div class="modes">
            <div class="tab der">
                <div>
                    <label>Цвет: <input type="text"/></label>
                    <span class="error">Не выбран график</span>
                    <span class="info"></span>
                </div>
                <div class="sub">
                    <label class="ang">Показывать угол: <input type="checkbox"/></label>
                    <span class="play">Проиграть: {ctrl_button states='plays stops'}</span>
                </div>
            </div>
            <div class="tab int">
                <div>
                    <label>Цвет: <input type="text"/></label>
                    <span class="error">Не выбран график</span>
                    <span class="info"></span>
                </div>
            </div>
        </div>

        <table class="controls">
            <tr class="new">
                <td class="rb">
                    <input type="radio" name="dekart"/>
                </td>
                <td class="cb">
                    <input type="checkbox"/>
                </td>
                <td class="cp">
                    <input type="text"/>
                </td>
                <td class="val">
                </td>
                <td>
                    <span class="buttons">
                        {ctrl_button action='edit' title='Редактировать'}
                        {ctrl_button action='accept' title='Принять'}
                        {ctrl_button action='remove' title='Удалить'}
                    </span>
                </td>
            </tr>

            <tr class="add">
                <td colspan="3">
                </td>
                <td>
                    y = <input type="text"/>
                </td>
                <td>
                    <span class="buttons">
                        {ctrl_button action='accept' title='Сохранить'}
                        {ctrl_button action='remove' title='Очистить'}
                    </span>
                </td>
            </tr>

        </table>


        <div class="help">
            <a href="#" class="h">справка</a>

            <div class="content">
                <p>
                    Приложение умеет строить графики функций вида y=f(x). Могут быть использованы любые из
                    функций, описанных в приложении «<a href="#" pageIdent="Calculator">калькулятор</a>».
                </p>
                <table class="colored">
                    <thead>
                        <tr>
                            <th>Математический синтаксис</th>
                            <th>Программный синтаксис</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>y = e\sup{cosx}</td>
                            <td>y = exp(cos(x))</td>
                        </tr>
                        <tr>
                            <td>y = x\sup{3} + \kor{|x|}</td>
                            <td>y = pow(x,3) + sqrt(abs(x))</td>
                        </tr>
                        <tr>
                            <td>y = x\sup{2} - sin(2x) - &pi;</td>
                            <td>y = sq(x) - sin(2*x) - pi</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>