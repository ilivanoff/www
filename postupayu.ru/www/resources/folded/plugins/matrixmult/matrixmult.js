$(function() {
    var FMANAGER = PsFoldingManager.FOLDING('pl', 'matrixmult');
    var STORE = FMANAGER.store();
    
    function MatrixManager($matrix) {
        var _this = this;
    
        this.setMarked = function(marked) {
            $matrix.toggleClass('marked', !!marked)
        }
    
        this.selectRow = function(num) {
            $($matrix.find('tr').get(num - 1)).find('td').addClass('selected');
        }

        this.selectCol = function(num) {
            $matrix.find('tr').each(function(){
                var $tr = $(this);
                var $td = $($tr.find('td').get(num-1));
                $td.addClass('selected');
            });
        }
    
        this.selectCell = function($td) {
            $td.addClass('selected');
        }
    
        this.getRowNums = function(row) {
            var result = [];
            $($matrix.find('tr').get(row - 1)).find('td').each(function(){
                result.push(_this.getValueTd($(this)));
            });
            return result;
        }
    
        this.getColNums = function(col) {
            var result = [];
            $matrix.find('tr').each(function(){
                var $tr = $(this);
                var $td = $($tr.find('td').get(col-1));
                result.push(_this.getValueTd($td));
            });
            return result;
        }
    
        this.clearSelection = function() {
            $matrix.find('td.selected').removeClass('selected');
        }
    
        this.iterateCells = function(callback) {
            $matrix.find('tr').each(function(row) {
                var $tr = $(this);
                $tr.find('td').each(function(col) {
                    var $td = $(this);
                    callback.call($td, $td, row+1, col+1);
                });
            });
        }

        this.resize = function(rows, cols, contentCallback) {
            var $tbody = $('<tbody>');
            $matrix.empty().append($tbody);
        
            for (var row = 1; row <= rows; row++) {
                var $tr = $('<tr>');
                for (var col = 1; col <= cols; col++) {
                    var $td = $('<td>');
                    //$td.attr('title', 'a'+row+col);
                    $tr.append($td);
                    if($.isFunction(contentCallback)) {
                        contentCallback.call($td, $td, row, col);
                    }
                }
                $tbody.append($tr);
            }
        }

        this.resizeWithInput = function(rows, cols, callback) {
            this.resize(rows, cols, function($td, row, coll) {
                var $input = $('<input>').attr('type', 'text').val(0);
                $input.
                keyup(function(){
                    callback.call($td, $td, row, coll);
                }).
                focus(function(){
                    $input.select();
                }).
                blur(function() {
                    _this.setValueTd($td, _this.getValueTd($td));
                });
                $td.append($input);
            });
        }
    
        this.getSize = function() {
            var rows = $matrix.find('tr').size();
            var cols = $matrix.find('tr:first td').size();
            return [rows, cols];
        }
    
        this.setValueTd = function($td, value) {
            if($td.hasChild('input')) {
                $td.find('input').val(value);
            }
            else 
            if($td.hasChild('a')){
                $td.find('a').html(value);
            }
            else{
                $td.html(value);
            }
        }
    
        this.getValue = function(row, col){
            var $row = $($matrix.find('tr').get(row-1));
            var $col = $($row.find('td').get(col-1));
            return this.getValueTd($col);
        }
    
        this.getValueTd = function($td) {
            var value = $td.hasChild('input') ? $td.find('input').val() : $td.html();
            return PsIs.number(value) ? parseInt(value) : 0;
        }
    
        this.fillWithData = function(data) {
            this.iterateCells(function($td, row, col) {
                var value = $.isFunction(data) ? data.call($td, $td, row, col) : data['c'+row+'x'+col];
                _this.setValueTd($td, value);
            });
        }

        this.fillRandom = function() {
            this.fillWithData(function(){
                return PsUtil.nextInt();
            });
        }

        this.fillZero = function() {
            this.fillWithData(function(){
                return 0;
            });
        }

        this.multiplyTo = function(managerB) {
            var lSize = this.getSize();
            var rSize = managerB.getSize();
        
            if(lSize[1]!=rSize[0]){
                return null;
            }

            var M = lSize[0];
            var N = lSize[1];
            var K = rSize[1];
        
            var res = {};
        
            for (var m = 1; m <= M; m++) {
                for (var k = 1; k <= K; k++) {
                    var sum = 0;
                    for (var i = 1; i <= N; i++) {
                        sum+=this.getValue(m, i) * managerB.getValue(i, k);
                    }
                    res['c'+m+'x'+k] = sum;
                }
            }
        
            return res;
        }
    
        return this;
    }


    /*
     * Размеры матриц: MxN и NxK
     */

    var MatrixMultManager = {
        M: 2,
        N: 2,
        K: 2,
    
        saveState: function() {
            STORE.set('size', [this.M, this.N, this.K]);
        },
    
        loadState: function() {
            var size = STORE.get('size', [2, 2, 2]);
            this.M = size[0];
            this.N = size[1];
            this.K = size[2];
            
        },
    
        dropState: function() {
            STORE.remove('size');
            this.loadState();
        },
    
        init: function() {
            var _this = this;
        
            this.loadState();
        
            this.mainDiv = $('.matrixmult');
        
            this.info = this.mainDiv.find('.info');
        
            this.sizeRes = this.mainDiv.find('tr.sizes td.hard');
        
            var $sizeInput = this.mainDiv.find('tr.sizes td input');
            this.$M = $($sizeInput.get(0));
            this.$N = $($sizeInput.get(1)).add($sizeInput.get(2));
            this.$K = $($sizeInput.get(3));
        
            $sizeInput.focus(function() {
                $(this).select();
            });
        
            this.$M.blur(function(){
                var $input = $(this);
                var num = _this.getInputNum($input);
                if(!num){
                    $input.val(_this.M);
                }else
                if(num!=_this.M) {
                    _this.M = num;
                    _this.recalcSize();
                }
            });
        
            this.$N.blur(function(){
                var $input = $(this);
                var num = _this.getInputNum($input);
                _this.$N.val(num);
                if(!num) {
                    _this.$N.val(_this.N);
                }else
                if(num!=_this.N){
                    _this.N = num;
                    _this.recalcSize();
                }
            });

            this.$K.blur(function(){
                var $input = $(this);
                var num = _this.getInputNum($input);
                if(!num){
                    $input.val(_this.K);
                }else
                if(num!=_this.K){
                    _this.K = num;
                    _this.recalcSize();
                }
            });
        
        
            new ButtonsController(this.mainDiv.find('.controls>button, .ctrl_descr>button'), {
                id: 'matrix_mult',
                ctxt: this,
                click: function(action, isOn) {
                    switch (action) {
                        case 'random':
                            this.onRandom();
                            break;
                        case 'random_table':
                            this.onRandomTable();
                            break;
                        case 'clear':
                            this.onClear();
                            break;
                        case 'default':
                            this.onDefault();
                            break;
                        case 'info':
                            _this.info.setVisible(isOn);
                            break;
                    }

                }
            });
        
            this.sumStr = this.mainDiv.find('.cell_detail');

            this.managerA = new MatrixManager(this.mainDiv.find('.matrixA'));
            this.managerB = new MatrixManager(this.mainDiv.find('.matrixB'));
            this.managerC = new MatrixManager(this.mainDiv.find('.matrixC'));
        
            this.recalcSize();
        },
    
        getInputNum: function($input) {
            var num = $input.val();
            return !PsIs.number(num) || num < 0 || num > 5 ? null : parseInt(num);
        },
    
        mark: function($td, row, col) {
            this.managerA.clearSelection();
            this.managerA.setMarked(true);
            this.managerA.selectRow(row);
        
            this.managerB.clearSelection();
            this.managerB.setMarked(true);
            this.managerB.selectCol(col);

            this.managerC.clearSelection();
            this.managerC.setMarked(true);
            this.managerC.selectCell($td);
        
            var Arow = this.managerA.getRowNums(row);
            var Bcol = this.managerB.getColNums(col);
            var strRes = '';
            var strRes2 = '';
            var n1, n2, mult;
            for (var i = 0; i < Arow.length; i++) {
                n1 = Arow[i];
                n2 = Bcol[i];
                mult = n1 * n2;
            
                strRes += strRes ? ' + ' : '';
                strRes2 += strRes2 ? ' + ' : '';
            
                strRes = strRes + (n1 >= 0 ? n1 : '('+n1+')')+'&sdot;'+(n2 >= 0 ? n2 : '('+n2+')');
                strRes2 = strRes2 + (mult >= 0 ? mult : '('+mult+')');
            }
            strRes = this.managerC.getValueTd($td) + ' = ' + strRes + ' = ' + strRes2;
            this.sumStr.html(strRes);
        },
    
        clearMark: function() {
            this.managerA.setMarked(false);
            this.managerB.setMarked(false);
            this.managerC.setMarked(false);
        
            this.sumStr.html('&nbsp;');
        },
    
        processMiltiply: function() {
            var res = this.managerA.multiplyTo(this.managerB);
            this.managerC.fillWithData(res);
        },
    
        recalcSize: function() {
            var _this = this;
        
            this.$M.val(this.M);
            this.$N.val(this.N);
            this.$K.val(this.K);
        
            this.managerA.resizeWithInput(this.M, this.N, function($td, row, col) {
                _this.processMiltiply();
            });
            this.managerB.resizeWithInput(this.N, this.K, function() {
                _this.processMiltiply();
            });
            this.managerC.resize(this.M, this.K, function($td, row, col) {
                $td.hover(function(){
                    _this.mark($td, row, col);
                },
                function(){
                    _this.clearMark();
                });
            });

            this.sizeRes.html(this.M + ' x ' + this.K);
            this.processMiltiply();
            this.clearMark();
            this.saveState();
        },
    
        onRandom: function() {
            this.managerA.fillRandom();
            this.managerB.fillRandom();
            this.processMiltiply();
        },
    
        onRandomTable: function() {
            this.M = Math.floor(Math.random()*10)%5 + 1;
            this.N = Math.floor(Math.random()*10)%5 + 1;
            this.K = Math.floor(Math.random()*10)%5 + 1;
            this.recalcSize();
            this.onRandom();
        },
    
        onClear: function() {
            this.managerA.fillZero();
            this.managerB.fillZero();
            this.managerC.fillZero();
        },
    
        onDefault: function() {
            this.dropState();
            this.recalcSize();
        }
    }


    MatrixMultManager.init();
});
