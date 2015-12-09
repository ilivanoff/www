$(function() {
    $('select').change(function() {
        var $select =  $(this);
        var $selected = $select.children(':selected');
        $select.disable();
        location.href = $selected.val();
    });
});