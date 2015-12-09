<div class="kinemat_plugin">
    {dekart}

    <div class="hg-next">
        <span class="help noselect">Режим<span class="q">?</span></span>: <span class="hg"></span>
    </div>

    <div class="calcs">
        <label class="tr1">
            <input type="radio" class="rb" name="kinemat_plugin_tr" checked="checked"/>
            &alpha;=<span class="val a"></span>&deg; &nbsp; 
            H=<span class="val h">0</span> [м] &nbsp; 
            &tau;\sub{H}=<span class="val th">0</span> [c] &nbsp; 
            L=<span class="val l">0</span> [м] &nbsp; 
            &tau;\sub{L}=<span class="val tl">0</span> [м]
        </label>
        <label class="tr2">
            <input type="radio" class="rb" name="kinemat_plugin_tr"/>
            &alpha;=<span class="val a"></span>&deg; &nbsp; 
            H=<span class="val h">0</span> [м] &nbsp; 
            &tau;\sub{H}=<span class="val th">0</span> [c] &nbsp; 
            L=<span class="val l">0</span> [м] &nbsp; 
            &tau;\sub{L}=<span class="val tl">0</span> [м]
        </label>
    </div>

    <div class="hg-self">
        <p>
            <a href="#trajectory">траектория</a> &mdash; 
            демонстрируется, что радиус-вектор положения точки в любой момент времени t
            есть сумма трёх векторов: \vect{r} = \vect{r\sub{0}} + \vect{v\sub{0}}t + \vect{g}t\sup{2}&frasl;2.
        </p>
        <p>
            <a href="#speed">скорость и ускорение</a> &mdash; 
            демонстрируется, что скорость точки в любой момент времени t
            есть сумма двух векторов: \vect{v} = \vect{v\sub{0}} + \vect{g}t, 
            а ускорение \vect{g} остаётся постоянным.
        </p>
        <p>
            <a href="#projections">проекции скорости</a> &mdash; 
            демонстрируется, что при движении тела в поле тяжести Земли меняется только вертикальная
            составляющая его скорости.
        </p>
        <p>
            Если установить начальную высоту бросания h0 равной нулю, то будет 
            показана альтернативная траектория, по которой тело может попать в конечную точку.
        </p>

        <div class="close">
            <span>закрыть</span>
        </div>
    </div>


    <div class="controls">
        <div class="box t timing">
            <div class="info">Время t=<span class="val t"></span> [с]</div>
            <div class="slider t"></div>
        </div>

        <div class="box a">
            <div class="info">Угол бросания &alpha;=<span class="val a"></span>&deg;</div>
            <div class="slider a"></div>
        </div>

        <div class="box h0">
            <div class="info">Начальная высота H\sub{0}=<span class="val h0"></span> [м]</div>
            <div class="slider h0"></div>
        </div>

        <div class="box v0">
            <div class="info">Начальная скорость v\sub{0}=<span class="val v0"></span> [м/с]</div>
            <div class="slider v0"></div>
        </div>

        <div class="box g">
            <div class="info">Ускорение свободного падения g=<span class="val g"></span> [м/с\sup{2}]
                <select class="planets">
                    <option value="">--</option>
                    {foreach $g as $name=>$val}
                        {$rv = round($val, 1)}
                        <option value="{$rv}">{$name} [{$rv}]&nbsp;</option>
                    {/foreach}
                </select>
            </div>
            <div class="slider g"></div>
        </div>
    </div>

</div>