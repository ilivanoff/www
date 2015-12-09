<div class="matrixmult">

    <h2>
        Умножение матриц
    </h2>

    <div class="controls">
        {ctrl_button action='random' name='dice' hoverable='1' title='Случайное заполнение'}
        {ctrl_button action='random_table' name='dice_table' hoverable='1' title='Случайное заполнение и изменение размеров матриц'}
        {ctrl_button action='clear' name='clear2' hoverable='1' title='Сброс значений'}
        {ctrl_button action='default' name='favorite' hoverable='1' title='Сброс значений и размеров матриц'}
        {*ctrl_button action='popup' name='popup' hoverable='1' popup='1' title='Краткая информация о матрицах'*}
        {ctrl_button action='info' name='info' type='trigger' title='Краткая информация о матрицах'}
    </div>

    <div class="info">
        <h5>О приложении</h5>
        <p>
            Данное приложение специально разработано мною для того, чтобы помочь Вам лучше разобраться
            с темой "умножение матриц". Надеюсь после того, как Вы "поиграетесь" ним, у Вас появится интуиция в данном вопросе.
        </p>
        <p>
            Вашему вниманию представлены две перемножающиеся матрицы A и В, а также результат их умножения &mdash; матрица C. Матрицы A и В можно 
            модифицировать, кликнув на соответствующую ячейку. Наведя на ячейку матрицы С будет подсвечена строка 
            матрицы A и столбец матрицы B, умножением которых получена данная ячейка. Под матрицами также будет дана расшифровка.
        </p>
        <p>
            Над матрицами указаны их размеры. Вы также можете их менять, кликнув на соотетствующую размерность. Допустимые значения &mdash; от 1 до 5.
            Размер матрицы C вычисляется автматически.
        </p>

        <h5>Кнопки управления</h5>
        <div class="ctrl_descr">
            {ctrl_button action='random' name='dice' hoverable='1' title='Случайное заполнение'} &mdash;
            заполняет ячейки матриц случайными числами
        </div>
        <div class="ctrl_descr">
            {ctrl_button action='random_table' name='dice_table' hoverable='1' title='Случайное заполнение с изменением размеров матриц'} &mdash;
            то-же, что предыдущая кнопка, плюс случайным образом меняются размеры матриц
        </div>
        <div class="ctrl_descr">
            {ctrl_button action='clear' name='clear2' hoverable='1' title='Сброс значений'} &mdash;
            сбрасывает значения в ячейках матрицы
        </div>
        <div class="ctrl_descr">
            {ctrl_button action='default' name='favorite' hoverable='1' title='Установка настроек по умолчанию'} &mdash;
            то-же, что предыдущая кнопка, плюс сбрасывает размеры матриц до 2x2
        </div>
        <p>
            Все эти кнопки случайной генерации добавлены мною как раз для того, чтобы акцентировать цель приложения на привыкании к матрицам,
            а не на подсчётах конкретных матриц. Но, если угодно, можете использовать их и с этой целью:)
        </p>

        <h5>Общие замечания</h5>
        <p>
            Данное приложение, например, наглядно демонстрирует правило: результатом умножения матриц размерами m<small>x</small>n и n<small>x</small>k является матрица 
            с размерами m<small>x</small>k. А, если словами: высота результирующей матрицы равна высоте первой матрицы, ширина равна ширине второй матрицы.
        </p>
        <p>
            Также данное приложение демонстрирует формулу для вычисления значений результирующей матрицы, которую без {strike}пол литра{/strike} наглядного примера
            довольно сложно представить:
        </p>
        \[c_{ij} = \sum_{r=1}^{n} a_{ir}b_{rj} ~~(i=1,2,...,m;~j=1,2,...,k)\]
        <p>
            Пожалуйста, работая с приложением обратите внимание на эти моменты.
        </p>

    </div>

    <table class="field">
        <tr class="sizes">
            <td>
                <input type="text"/> x <input type="text"/>
            </td>
            <td>
            </td>
            <td>
                <input type="text"/> x <input type="text"/>
            </td>
            <td>
            </td>
            <td class="hard">
            </td>
        </tr>

        <tr>
            <td>

                {*A*}
                <table class="matrix matrixA">
                    <tbody>
                        <tr>
                            <td>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </td>
            <td>
                X
            </td>
            <td>

                {*B*}
                <table class="matrix matrixB">
                    <tbody>
                        <tr>
                            <td>
                            </td>
                        </tr>
                    </tbody>
                </table>

            </td>
            <td>
                =
            </td>
            <td>

                {*C*}
                <table class="matrix matrixC">
                    <tbody>
                        <tr>
                            <td>
                                13
                            </td>
                        </tr>
                    </tbody>
                </table>

            </td>
        </tr>

    </table>

    <div class="cell_detail">
    </div>

</div>