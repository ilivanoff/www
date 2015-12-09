{task}
<p>
    Тело брошено с поверхности земли под углом к горизонту.
    После преодоления максимальной высоты подъёма тело оказалось в точке А траектории,
    находящейся на высоте H. 
    В этот момент тело имело скорость v, направленную под углом &alpha; к горизонту:
</p>

{postimgb type='tr' ident='kinemtochki3' name='VH.png'}

<p>
    Найдите время достижения этой точки траектории.
    Также определите момент времени, в который тело оказалось на высоте H в первый раз.
</p>

<p>
    Пусть тело было брошено с начальной скоростью v\sub{0}.
    Начало координат поместим в точке бросания. Координатные оси направим так, как показано на рисунке:
</p>

{postimgb type='tr' ident='kinemtochki3' name='VH2.png'}

<p>
    Запишем выражения для проекции скорости v\sub{y} и координаты y:
</p>

\[v_y&=v_{0y}-gt, y&=v_{0y}t-\frac{gt^2}{2}\] {b}(3){/b}

<p>
    Пусть T &mdash; момент времени, в который тело находилось на высоте H. В этот момент времени
    проекция скорости на вертикальное направление: v\sub{y}=&minus;v\~sin&alpha;. Имеем систему:
</p>

\[
\left \lbrace
\begin{aligned}
v_{0y}-gT&=-v\sin\alpha,\\
H&=v_{0y}T-\frac{gT^2}{2}
\end{aligned}
\right.
\]
{b}(4){/b}
<p>
    Выразив v\sub{0y} из первого уравнения и подставив во второе, получим уравнение для определения T:
</p>

\[
(gT-v\sin\alpha)T-\frac{gT^2}{2}=H
\]

<p>
    Это уравнение сводится к квадратному:
</p>

\[
\frac{g}{2}T^2-v\sin\alpha~T-H=0
\]

<p>
    Данное квадратное уравнение имеет два корня:
</p>

\[
T_{1,2}=\frac{1}{g}(v\sin\alpha\pm\sqrt{v^2\sin^2\alpha+2gH})
\]
{b}(5){/b}
<p>
    Один из корней &mdash; всегда отрицателен и не подходит по смыслу задачи.
    Таким образом, искомый момент времени определяется выражением:
</p>

\[
T=\frac{1}{g}(v\sin\alpha+\sqrt{v^2\sin^2\alpha+2gH})
\]

<p>
    Зная время T, можем найти проекцию вектора начальной скорости на вертикальное направление:
</p>

\[
v_{0y}=gT-v\sin\alpha = \sqrt{v^2\sin^2\alpha+2gH}
\]
{b}(8){/b}
<p>
    Теперь определим моменты времени, в которые тело было на высоте H, для этого повторно воспользуемся законом движения вдоль оси y:
</p>

\[
H=v_{0y}t-\frac{gt^2}{2}
\]

{b}(6){/b}
<p>
    Это квадратное уравнение имеет два корня:
</p>

\[
t_{1,2}=\frac{1}{g}\Bigl(v_{0y}\pm\sqrt{v_{0y}^2-2gH}\Bigr)
\]

<p>
    С учётом выражения для v\sub{oy} получим:
</p>

\[
t_{1,2}=\frac{1}{g}\Bigl(\sqrt{v^2\sin^2\alpha+2gH}\pm v\sin\alpha\Bigr)
\]
{b}(7){/b}
<p>
    Один из корней, конечно, совпадает с моментом времени, когда тело было в точке A.
    Второй корень соответствует моменту времени, когда тело достигло высоты H в первый раз.
</p>



<h4>Рассуждения</h4>

<p>
    Эту задачу я специально рассмотрел для того, чтобы показать, почему при решении иногда получается два корня.
    Такие рассуждения справедливы не только для кинематики.
</p>

<p>
    Итак, мы сказали: "Я вижу тело, которое находится в точке А траектории на высоте H и имеет в этой точке скорость \vect{v}
    Математика, ответь мне, в какой момент времени тело было на высоте H?"
</p>

{solut}
{/solut}

{ans}
{/ans}
{/task}


{postimgb type='tr' ident='kinemtochki3' name='hh0.png'}

\[
y=h_0+v_0\sin\alpha t - \frac{gt^2}{2}
\]

\[
v_y=v_0\sin\alpha t - gt; ~y=h_0+v_0\sin\alpha t - \frac{gt^2}{2}
\]

\[
T=\frac{v_0\sin\alpha}{g}
\]

\[
h_{max}=h_0+v_0\sin\alpha T - \frac{gT^2}{2}=h_0+\frac{v_0^2\sin^2\alpha}{2g}
\]

\[
\begin{aligned}
t_{1,2}&=\frac{v_0\sin\alpha}{g}\Bigl(1\pm\sqrt{1-\frac{2g(H-h_0)}{v_0^2\sin^2\alpha}}\Bigr)=\\
&=\frac{v_0\sin\alpha}{g}\Bigl(1\pm\sqrt{1-\frac{H-h_0}{h_{max}-h_0}}\Bigr)=\\
&=\frac{v_0\sin\alpha}{g}\Bigl(1\pm\sqrt{\frac{h_{max}-H}{h_{max}-h_0}}\Bigr)
\end{aligned}
\]

\[
H=h_0+v_0\sin\alpha t - \frac{gt^2}{2}
\]

\[
\frac{g}{2}t^2-v_0\sin\alpha t +(H-h_0)=0
\]

\[
\begin{aligned}
v_y&=v_0\sin\alpha-gt\\
H&=h_0+v_0\sin\alpha t - \frac{gt^2}{2}
\end{aligned}
\]

<p>
    Это уравнение, как и следовало ожидать, имеет два корня:
</p>

\[
t_{1,2}=\frac{1}{g}\Bigl(v_0\sin\alpha\pm\sqrt{v_0^2\sin^2\alpha-2g(H-h_0)}\Bigr)
\]

\[
\begin{aligned}
&v_0^2\sin^2\alpha-2g(h_{max}-h_0)=0,\\
&h_{max}=\frac{v_0^2\sin^2\alpha}{2g}+h_0
\end{aligned}
\]



\[
h_{max}=\frac{v_0^2\sin^2\alpha}{2g}+h_0
\]


\[
v_0^2\sin^2\alpha = 2g(h_{max}-h_0)
\]


\[
t_{1,2}=\frac{1}{g}\Bigl(v_0\sin\alpha\pm\sqrt{2g(h_{max}-H)}\Bigr)
\]


\[
\begin{aligned}
T_{+}&=\frac{1}{g}(v\sin\alpha+\sqrt{v^2\sin^2\alpha+2gH})>0\\
T_{-}&=\frac{1}{g}(v\sin\alpha-\sqrt{v^2\sin^2\alpha+2gH})<0
\end{aligned}
\]


\[
\begin{aligned}
v_{0y+}&=gT_+-v\sin\alpha = \sqrt{v^2\sin^2\alpha+2gH}\\
v_{0y-}&=gT_- -v\sin\alpha = -\sqrt{v^2\sin^2\alpha+2gH}
\end{aligned}
\]

\[v_{0y+}=-v_{0y-}\]


\[
t_{1,2}=\frac{1}{g}\Bigl(v_0\sin\alpha\pm v_0\sin\alpha\Bigr)
\]


\[
\begin{aligned}
\tau&=T_{+} + |T_{-}| = T_{+} - T_{-} =\\
&=\frac{1}{g}(v\sin\alpha+\sqrt{v^2\sin^2\alpha+2gH}) - \frac{1}{g}(v\sin\alpha-\sqrt{v^2\sin^2\alpha+2gH})=\\
&=\frac{2}{g}\sqrt{v^2\sin^2\alpha+2gH}
\end{aligned}
\]


\[
\tau=\frac{2v_{0y}}{g}
\]

\[
\begin{aligned}
t_{1,2}
&=\frac{1}{g}\Bigl(v_0\sin\alpha\pm\sqrt{v_0^2\sin^2\alpha-2g(H-h_0)}\Bigr)=\\
&=\frac{1}{g}\Bigl(v_0\sin\alpha\pm\sqrt{v_0^2\sin^2\alpha+2g(h_0-H)}\Bigr)=\\
&=\frac{v_0\sin\alpha}{g}\Biggl(1\pm\sqrt{1+\frac{2g(h_0-H)}{v_0^2\sin^2\alpha}}\Biggr)
\end{aligned}
\]


\[t=\sqrt{\frac{2H}{g}}\]

{postimgb type='tr' ident='kinemtochki3' name='VH3.png'}

\[
x = x_0 + v_0t+\frac{at^2}{a}
\]

\[
S = v_0t+\frac{at^2}{a}
\]

\[
S = v_0t+\frac{at^2}{a}
\]

\[
t_+=\frac{-v_0+\sqrt{v_0^2+2aS}}{a}
\]

\[
t_-=\frac{-v_0-\sqrt{v_0^2+2aS}}{a}
\]

\[
0 = -S + v_0t+\frac{at^2}{a}
\]

\[
0 = S_0 + v_0t+\frac{at^2}{a}
\]

\[
0 = S_0 + v_0t+\frac{at^2}{a}
\]

<hr/>

\[
\vec{r}=\vec{r}_c+\vec{v}_ct+\frac{\vec{g}t^2}{2}
\]

\[
y = y_0+v_{0y}t+\frac{g_yt^2}{2}
\]

\[
y = v_0t-\frac{gt^2}{2}
\]

\[
-h = v_0t-\frac{gt^2}{2}
\]

\[
\left 
\lbrace 
\begin{aligned}
\vec{r}&=\vec{r}_b+\vec{v}_bt+\frac{\vec{g}t^2}{2}\\
\vec{v}&=\vec{v}_b+\vec{g}t
\end{aligned}
\right.
\]

\[
\left 
\lbrace 
\begin{aligned}
\vec{r}&=\vec{r}_b+\vec{v}_b(t-t_b)+\frac{\vec{g}(t-t_b)^2}{2}\\
\vec{v}&=\vec{v}_b+\vec{g}(t-t_b)
\end{aligned}
\right.
\]

\[
\left 
\lbrace 
\begin{aligned}
\vec{r}_a&=\vec{r}_b-\vec{v}_bt_b+\frac{\vec{g}t_b^2}{2}\\
\vec{v}_a&=\vec{v}_b-\vec{g}t_b
\end{aligned}
\right.
\]

\[
\left 
\lbrace 
\begin{aligned}
\vec{r}_c&=\vec{r}_b+\vec{v}_b(t_c-t_b)+\frac{\vec{g}(t_c-t_b)^2}{2}\\
\vec{v}_c&=\vec{v}_b+\vec{g}(t_c-t_b)
\end{aligned}
\right.
\]

\[\frac{g}{2}t^2-v_0t-h =0\]

\[
t_{1,2}=\frac{1}{g}(v_0\pm\sqrt{v_0^2+2gh})
\]

\[
t=\frac{1}{g}(v_0-\sqrt{v_0^2+2gh})
\]

\[
t=\frac{1}{g}(v_0+\sqrt{v_0^2+2gh})
\]

\[v_y=v_{0}-gt, y=v_{0}t-\frac{gt^2}{2}\]

\[
\left 
\lbrace 
\begin{aligned} 
u&=v_{0}-gt, \\
h&=v_{0}t-\frac{gt^2}{2}
\end{aligned} 
\right.
\]

\[
\frac{g}{2}t^2-v_0t+h=0
\]

\[
t_{1,2}=\frac{1}{g}(v_0\pm\sqrt{v_0^2-2gh})
\]

\[
t=\frac{1}{g}(v_0-\sqrt{v_0^2-2gh})
\]

\[
\begin{aligned}
\frac{1}{g}(v_0-v)&=\frac{1}{g}(v_0-\sqrt{v_0^2-2gh})\\
v&=\sqrt{v_0^2-2gh}\\
v_0&=\sqrt{v^2+2gh}\\
\end{aligned}
\]

\[
t=\frac{1}{g}(-v+\sqrt{v_0^2+2gh})
\]

\[v_y=v-gt, y=h+vt-\frac{gt^2}{2}\]

\[0=h+vt-\frac{gt^2}{2}\]

\[
x=x_0+v_{0x}t+\frac{a_xt^2}{2}
\]

\[
x=s+vt-\frac{at^2}{2}
\]


\[
\begin{aligned}
\frac{\Delta\vec{r}}{\Delta t}
&=\frac{\vec{r}(t+\Delta t)-\vec{r}(t)}{\Delta t}=\\
&=\frac{1}{\Delta t}
\Biggl(
\vec{r}_0+\vec{v}_0(t+\Delta t-t_0)+\frac{\vec{a}(t+\Delta t-t_0)^2}{2}-
\vec{r}_0-\vec{v}_0(t-t_0)-\frac{\vec{a}(t-t_0)^2}{2}
\Biggr)=\\
&=\frac{1}{\Delta t}
\Biggl(
\vec{v}_0\Delta t+\frac{\vec{a}\Delta t(2t+\Delta t-2t_0)}{2}
\Biggr)=\vec{v}_0+\vec{a}\Bigl(t-t_0+\frac{\Delta t}{2}\Bigr)
\end{aligned}
\]

\[
h=(u+gt)t-\frac{gt^2}{2}=ut+\frac{gt^2}{2}
\]

\[
t_{1,2}=\frac{1}{g}(-u\pm\sqrt{u^2+2gh})
\]

\[
t=\frac{1}{g}(-u+\sqrt{u^2+2gh})
\]


\[v_y=u-g(t-\tau), y=h+u(t-\tau)-\frac{g(t-\tau)^2}{2}\]

\[
0=h-u\tau-\frac{g\tau^2}{2}
\]


\[t_{1,2}=\frac{1}{g}(u\pm\sqrt{u^2+2gh})\]

\[t=\frac{1}{g}(u-\sqrt{u^2+2gh})\]

\[|t|=-t=\frac{1}{g}(-u+\sqrt{u^2+2gh})\]

\[v_y=v_{0y}-gt, y=y_0+v_{0y}t-\frac{gt^2}{2}\]
\[v_y=v-gt, y=h+vt-\frac{gt^2}{2}\]
