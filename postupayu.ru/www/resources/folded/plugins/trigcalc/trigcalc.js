$(function() {
    
    function TrigCalcPlugin($BODY) {
        
        function trimNumber(x) {
            return PsHtml.num2str(PsMath.round(x, 2));
        }
        
        function getValue(num, inputVal, selectVal) {
            var grads = selectVal==1;

            var radians;
            switch (num) {
                case 0:
                    return grads ?
                    (trimNumber(PsMath.gradToRad(inputVal))) + " радиан" :
                    (trimNumber(PsMath.radToGrad(inputVal))) + "&deg;";
                case 1:
                    inputVal = grads ? PsMath.gradToRad(inputVal) : inputVal;
                    return trimNumber(Math.sin(inputVal));
                case 2:
                    inputVal = grads ? PsMath.gradToRad(inputVal) : inputVal;
                    return trimNumber(Math.cos(inputVal));
                case 3:
                    inputVal = grads ? PsMath.gradToRad(inputVal) : inputVal;
                    return Math.round(Math.cos(inputVal))==0 ? "Не определён" : trimNumber(Math.tan(inputVal));
                case 4:
                    if(inputVal<-1 || inputVal>1) return PsHtml.span('Значение должно быть в интервале [&minus;1, 1]', 'red');
                    radians = Math.asin(inputVal);
                    return trimNumber(radians) + " радиан, "+trimNumber(PsMath.radToGrad(radians)) + "&deg;";
                case 5:
                    if(inputVal<-1 || inputVal>1) return PsHtml.span('Значение должно быть в интервале [&minus;1, 1]', 'red');
                    radians = Math.acos(inputVal);
                    return trimNumber(radians) + " радиан, "+trimNumber(PsMath.radToGrad(radians)) + "&deg;";
                case 6:
                    radians = Math.atan(inputVal);
                    return trimNumber(radians) + " радиан, "+trimNumber(PsMath.radToGrad(radians)) + "&deg;";
            }
        }
        
        var recalc = function() {
            $BODY.find("tr").each(function(num, elem){
                var inputVal = $(elem).find("input").val().replace(',', '.');
                var selectVal = $(elem).find("select").val();
                var $result = $("td:last-child", elem);
                if (PsIs.number(inputVal)) {
                    $result.html(getValue(num, strToInt(inputVal, 0), strToInt(selectVal, 0)));
                } else {
                    $result.html(inputVal=='' ? '' : PsHtml.span('Введено не число', 'red'));
                }
            });
        }
        
        $BODY.find("input, select").keyup(recalc);
        $BODY.find("select").change(recalc);
        recalc();
    }

    new TrigCalcPlugin($('.trig-calc'));
});
