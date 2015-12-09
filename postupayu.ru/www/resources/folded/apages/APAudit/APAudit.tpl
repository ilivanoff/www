<div class="ps-tabs" id="ps-audit-tab">
    <div title="Поиск">
        {form form_id='AdminAuditSearchForm'}
    </div>

    <div title="Просмотр статистики" id="audit-statistic">
        <h5>Дата по:</h5>
        <input class="ps-datetime-picker"/>
        <div class="stat-buttons separetedtb">
            <button>Найти</button>
            <button>Сбросить</button>
        </div>
        <div class="results">
            <h4>Статистика:</h4>
            <table class="colored sortable">
                <thead>
                    <tr>
                        <th>Аудит</th>
                        <th>Действие</th>
                        <th>Кол-во записей</th>
                    </tr>
                </thead>
                <tbody>{*Будет перестроено с помощью js*}</tbody>
                <tfoot>
                    <tr>
                        <td colspan="2"></td>
                        <td class="total">50</td>
                    </tr>
                </tfoot>
            </table>
            <div class="take-dump-buttons separetedtb">
                <button data-size="{$portion}">Снять дамп ({$portion})</button>
            </div>
        </div>

        <br/>
        <h5>Дампы:</h5>
        {$dumps}
        <div class="dump-buttons separetedtb">
            <button>Перезагрузить</button>
        </div>

    </div>
</div>
