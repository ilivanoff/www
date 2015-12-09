$(function() {
    
    //Смена временной зоны
    FormHelper.registerOnce({
        form: '#TzEditForm',
        onOk: 'Временная зона успешно обновлена',
        validator: {
            rules: {
                timezone: {
                    required: true,
                    notEqualTo: '#TzEditForm [name="timezone"] option.current'
                }
            },
            messages: {
                timezone: {
                    required: 'Необходимо выбрать временную зону',
                    notEqualTo: 'Временная зона совпадает с текущей'
                }
            }
        }
    });
    
    
    /*
     * Временные зоны
     */
    var $tz_helper = $('#tz_helper');
    
    var d = new Date();
    var offsetM = -d.getTimezoneOffset();
    var offsetS = offsetM * 60;
 
    $tz_helper.find('.local_tz').html(PsTimeHelper.getGmtPresentation(offsetS));
    
    var $local_tz_select = $tz_helper.find('.local_tz_select');
    
    var $timeZonesCombo = $('select.time_zones');
    var $optionsForOffset = $timeZonesCombo.find('option[offset="' + offsetS + '"]');
    
    if (isEmpty($optionsForOffset)) {
        $local_tz_select.append($('<span>').addClass('gray').html('К сожалению, для Вашего часового пояса не найдено временных зон.'));
    } else {
        $optionsForOffset.each(function() {
            var $option = $(this);
            var tzName = $option.val();
            var tzA = crA(null, 'Временная зона: '+tzName).html(tzName).clickClbck(function() {
                if ($timeZonesCombo.is(':enabled')) {
                    $timeZonesCombo.val(tzName);
                }
            });
            $local_tz_select.append(tzA).append('<br/>');
        });
    }
});