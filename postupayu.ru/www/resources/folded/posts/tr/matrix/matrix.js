$(function(){
    $('a.play').clickClbck(function() {
        AdvGraphPlugin.setMode('der');
        AdvGraphPlugin.addFunctions('x*x');
        AdvGraphPlugin.playDeriv();
    });
});