{*gallery dir='alekhin' lazy=1*}

<canvas id="example" width="501" height="381">Обновите браузер</canvas>

{*literal}
<script>
function get(url) {
try {
var xhr = new XMLHttpRequest();
xhr.open('GET', url, false);
xhr.send();
return xhr.responseText;
} catch (e) {
return ''; // turn all errors into empty results
}
}
  
alert(get('t.php'));
alert('here');

alert(PsObjects.toString(Worker));
var example = document.getElementById('example'); // Задаём контекст
var context       = example.getContext('2d');           // Контекст холста

context.save();
for (var x = 0.5; x <= example.width; x += 10) {
context.moveTo(x, 0);
context.lineTo(x, example.height);
}
    
for (var y = 0.5; y < example.height; y += 10) {
context.moveTo(0, y);
context.lineTo(example.width, y);
}
    
context.strokeStyle = "#eee";
context.stroke();
    
context.restore();

context.beginPath();
context.strokeStyle = "#000";   
context.lineWidth = 1;
context.moveTo(501, 0);
context.lineTo(501, 20);

context.moveTo(500.5, 30);
context.lineTo(500.5, 40);

context.moveTo(500, 50);
context.lineTo(500, 60);

context.moveTo(499.5, 70);
context.lineTo(499.5, 80);
context.stroke();
</script>
{/literal*}

{*psplugin name='pascal'}.{/psplugin*}

{*<div id='my-date-picker'></div>*}
