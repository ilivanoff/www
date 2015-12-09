$(function() {

    function TrigKrugManager($field, R) {
        this.CX = Math.round($field.width()/2);
        this.CY = Math.round($field.height()/2);
    
        this.P = $field.find('.P');
        this.INFO = $field.find('.info');
    
        this.putDot = function(alpha, r, cl) {
            var x = r * PsMath.cosGrad(alpha);
            var y = r * PsMath.sinGrad(alpha);
        
            var $dot = $('<div>').addClass('dot').appendTo($field);
            if (cl) {
                $dot.addClass(cl);
            }

            var left = this.CX + x - $dot.width()/2;
            var top =  this.CY - y - $dot.height()/2;

            $dot.css('left', left).css('top', top);
        }

        this.drawLine = function(alpha) {
            for (var r = 0; r <= R; r+=4) {
                this.putDot(alpha, r);
            }
        }

        this.putMarker = function(alpha) {
            this.putDot(alpha, R, 'marker');
        }
    

        this.drawAngle = function(from, grad) {
            if(grad==0) return;
            var to;
            for (var a = 0; a <= Math.abs(grad); a+=4) {
                to = from + (grad > 0 ? 1 : -1) * a;
                this.putDot(to, 60, 'gray');
            }
            //Стрелочка
            if (Math.abs(from-to) > 4) {
                to += grad > 0 ? -4 : 4;
                this.putDot(to, 63, 'gray');
                this.putDot(to, 57, 'gray');
                if (Math.abs(from-to) > 4) {
                    to += grad > 0 ? -4 : 4;
                    this.putDot(to, 66, 'gray');
                    this.putDot(to, 54, 'gray');
                }
            }
        }
    
        this.showP = function(alpha, content) {
            var $P = $('<div>').addClass('P').appendTo($field);
        
            var angHtml = content ? content : PsHtml.mathText('&alpha;');
        
            $P.html('P (<span class="cos">cos'+angHtml+'</span>, <span class="sin">sin'+angHtml+'</span>)');
        
            var r = R + 10;
            var cosVal = PsMath.cosGrad(alpha);
            var sinVal = PsMath.sinGrad(alpha);
            var x = r * cosVal;
            var y = r * sinVal;
        
            var W = $P.width();
            var H = $P.height();
        
            var left = this.CX + x;
            var top =  this.CY - y;
        
            switch (PsMath.getKvadrant(alpha)) {
                case 1:
                    top -= H;
                    break;
                case 2:
                    left-= W;
                    top -= H;
                    break;
                case 3:
                    left-= W;
                    break;
                case 4:
                    break;
            }
        
            $P.css('left', left).css('top', top);
            /*
        .attr('title', 
            'α=' + PsMath.round(alpha, 1) + 
            '°, cosα=' + PsMath.round(cosVal, 2) + 
            ', sinα=' + PsMath.round(sinVal, 2)
            );
            */
            return $P;
        }
    
        this.selectP = function(funcs) {
            $field.children('.P').find(funcs).addClass('active');
        }
    
        this.showAlpha = function(from, grad, sign) {
            var $alpha = $('<div>').addClass('alpha').
            html(PsHtml.mathText((sign && sign<0 ? '&minus; ' : '') + '&alpha;')).appendTo($field);
        
            var alpha = from + (grad/2);
            var r = 60 + 12;
            var cosVal = PsMath.cosGrad(alpha);
            var sinVal = PsMath.sinGrad(alpha);
            var x = r * cosVal;
            var y = r * sinVal;
        
            var W = $alpha.width();
            var H = $alpha.height();
        
            var left = this.CX + x;
            var top =  this.CY - y;
        
            switch (PsMath.getKvadrant(alpha)) {
                case 1:
                    top -= H/2;
                    break;
                case 2:
                    left-= W/2;
                    top -= H/2;
                    break;
                case 3:
                    left-= W/2;
                    break;
                case 4:
                    break;
            }
        
            $alpha.css('left', left).css('top', top);
        }
    
        this.markX = function(alpha, label) {
            var x = R * PsMath.cosGrad(alpha);
            var w = Math.abs(x);
            var $xMarker = $('<div>').addClass('mark X').appendTo($field).width(w);
            var top = this.CY - 1;
            var left = Math.round(this.CX + (x >= 0 ? 0 : x));
            $xMarker.css('left', left).css('top', top);
        
            if (label) {
                var $label = $('<div>').addClass('label X').html(label).appendTo($field);
                left = left + w/2 - $label.width()/2;
                var dY = PsMath.getKvadrant(alpha) < 3 ? 5 : -1*($label.height() + 2);
                top = top + dY;
                $label.css('left', left).css('top', top);
            }
        }

        this.perpToY = function(alpha) {
            var x = R * PsMath.cosGrad(alpha);
            var y = R * PsMath.sinGrad(alpha);

            var $yPerp = $('<div>').addClass('perp').appendTo($field).width(Math.abs(x));
            var top = Math.round(this.CY - y);
            var left = Math.round(this.CX + (x >= 0 ? 0 : x));
            $yPerp.css('left', left).css('top', top);
        }

        this.markY = function(alpha, label) {
            var y = R * PsMath.sinGrad(alpha);
            var h = Math.abs(y);
            var $yMarker = $('<div>').addClass('mark Y').appendTo($field).height(h);
            var top = Math.round(this.CY - (y >= 0 ? y : 0));
            var left = this.CX - 1;
            $yMarker.css('left', left).css('top', top);

            if (label) {
                var $label = $('<div>').addClass('label Y').html(label).appendTo($field);
                top = top + h/2 - $label.height()/2;
                var kv = PsMath.getKvadrant(alpha);
                var dX = kv==2 || kv==3 ? 6 : -1*($label.width() + 6);
                left = left + dX;
                $label.css('left', left).css('top', top);
            }
        }

        this.perpToX = function(alpha) {
            var x = R * PsMath.cosGrad(alpha);
            var y = R * PsMath.sinGrad(alpha);

            var $xPerp = $('<div>').addClass('perp').appendTo($field).height(Math.abs(y));
            var top = Math.round(this.CY - (y >= 0 ? y : 0));
            var left = Math.round(this.CX + x);
            $xPerp.css('left', left).css('top', top);
        }
    
        this.bindClickListener = function(callback) {
            var _this = this;
            $field.unbind('click').bind('click', function(e) {
                var $offset = $field.offset();
                var x =-_this.CX + (e.pageX - $offset.left);
                var y = _this.CY - (e.pageY - $offset.top);
                var Rpow2 = Math.pow(x, 2) + Math.pow(y, 2);
                if (Rpow2 > Math.pow(R, 2)) {
                    //out
                    return;
                }
                var alpha = PsMath.radToGrad(Math.acos(x/Math.sqrt(Rpow2)));
                alpha *= y<0 ? -1 : 1;
                callback.call(_this, alpha);
            });
        }
    
        this.info = function(alpha) {
            if (PsIs.number(alpha)) {
                var aVal = PsMath.round(alpha, 1);
                var sVal = (PsMath.round(PsMath.sinGrad(alpha), 2));
                var cVal = (PsMath.round(PsMath.cosGrad(alpha), 2));
            
                this.INFO.show();
            
                this.INFO.find('.a_val').html((aVal >=0 ? aVal : '&minus; '+Math.abs(aVal)) + '&deg;');
                this.INFO.find('.s_val').html(sVal >=0 ? sVal : '&minus; '+Math.abs(sVal));
                this.INFO.find('.c_val').html(cVal >=0 ? cVal : '&minus; '+Math.abs(cVal));
            } else {
                $('<div>').addClass('info').html(alpha).appendTo($field).show();
            }
        }
    
        this.showDot = function(alpha) {
            alpha = PsIntervals.angleTo0_360(alpha);
        
            this.clear();
            this.drawLine(alpha);
        
            this.showP(alpha);
            this.selectP('.sin, .cos');
        
            this.drawAngle(0, alpha);
            this.showAlpha(0, alpha);
            
            this.markX(alpha);
            this.markY(alpha);
        
            this.perpToY(alpha);
            this.perpToX(alpha);
            this.putMarker(alpha);
        
            this.info(alpha);
        }
    
        this.showDotReflection = function(alpha) {
            var A1 = Math.abs(alpha);
            var A2 = -A1;
        
            this.clear();
            this.drawLine(A1);
            this.drawLine(A2);
        
            this.showP(A1);
            this.showP(A2, '(&minus;' + PsHtml.mathText('&alpha;') + ')');
            this.selectP('.sin, .cos');
        
            this.drawAngle(0, A1);
            this.drawAngle(0, A2);
            this.showAlpha(0, A1);
            this.showAlpha(0, A2, -1);
            
            this.markX(A1);
            this.markY(A1);
            this.markY(A2);
        
            this.perpToY(A1);
            this.perpToY(A2);
            this.perpToX(A1);
            this.perpToX(A2);
        
            this.putMarker(A1);
            this.putMarker(A2);
        
        //        this.info(alpha);
        }
    
        this.showRule = function(piNa2, alpha, func, ans) {
            var axe = func == 'sin' ? 'Y' : 'X';
            var RIGHT = piNa2*90;
            var A = RIGHT + alpha;

            this.clear();
            this.drawLine(A);

            var pAng = A>90 || A<0 ? '(' + PsMath.piNa2Str(A, PsMath.sign(alpha)) + ')' : null;

            var $P = this.showP(A, pAng);
            this.selectP('.' + func);
        
            this.drawAngle(RIGHT, alpha);
            this.showAlpha(RIGHT, alpha);
        
            this['mark'+axe].call(this, A, ans);
            this['perpTo'+axe].call(this, A);
        
            this.putMarker(A);
            this.info($P.find('.'+func).html()+' = '+ans);
        }
    
        this.clear = function() {
            $field.children(':not(table.info)').remove();
            $field.children().hide();
        }
    }


    var TrigKrugPlugin = {
        R: 243,
    
        MODE: null,
        init: function() {
            var _this = this;
        
            this.BODY = $('.trigkrug_plugin');
            this.MANAGER = new TrigKrugManager(this.BODY.find('.field'), this.R);
        
            this.MANAGER.bindClickListener(function(A) {
                _this.circleClicked(A);
            });
        
            this.TABLE = this.BODY.find('table.funcs');
            this.TABLE.find('td').click(function() {
                _this.tableCellClicked($(this));
            });
        
            var $HGN = this.BODY.find('.hg-next');
            var ModeController = new HrefsGroup($HGN, 'next', {
                callback: function(mode) {
                    _this.doSetMode(mode);
                }
            });
        
            this.BODY.show();

            ModeController.callbackCall();
        },
    
        doSetMode: function(mode) {
            this.MODE = mode;
            this.circleClicked(45);
        },
    
        circleClicked: function(A) {
            this.clearTable();
            switch (this.MODE) {
                case 'P':
                    this.MANAGER.showDot(PsIntervals.angleTo0_360(A));
                    break;
                case 'chet':
                    this.MANAGER.showDotReflection(A);
                    break;
            }
        },
    
        clearTable: function() {
            this.TABLE.find('.selected, .marked').removeClass('selected marked');
        },
    
        tableCellClicked: function($td) {
            var $tr = $td.parent('tr');
            var func = $tr.data('f');
            var idx = $td.index();
            if (!func || idx==0) return;
        
            this.clearTable();
        
            $td.addClass(func).addClass('selected');
            $tr.find('td:first').addClass('marked');
            var $th = $(this.TABLE.find('tr:first th').get(idx)).addClass('marked');
        
            var n = parseInt($th.data('n'));
            var s = parseInt($th.data('s'));
        
            this.MANAGER.showRule(n, s*45, func, $td.html());
        }
    }

    TrigKrugPlugin.init();
});
