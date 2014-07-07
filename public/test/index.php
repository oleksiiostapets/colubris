<html>
<head>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
</head>

<body>


<script>

    // class Field
    function Field() {

        this.correct_line;
        var boxes = new Object();

        var div = $('#yes');
        div.css('width','204px')
              .css('height','306px')
              .css('background-color','red')
        ;

        this.addBox = function(id,color) {
            if (typeof current_position == 'undefined') {
                current_position = 1;
            } else {
                current_position = current_position + 1;
            }
            //console.log('current_position = ' + current_position);
            var box = new Box(id,color);
            box.field = this;
            box.position = current_position;
            boxes[id] = box;
        }

        this.repaint = function() {
            position_to_find = 1;
            while (position_to_find <= current_position) {
                $.each(boxes,function(i,e){
                    var box = e;
                    //console.log(e);
                    if (box.position == position_to_find) {
                        div.append(box.getElement());
                    }
                });
                //console.log('position_to_find = ' + position_to_find);
                position_to_find = position_to_find + 1;
            }
        }

        this.isAnyBoxSelected = function() {
            var is_any_selected = false;
            $.each(boxes,function(i,e){
                if (e.selected) {
                    is_any_selected = true;
                }
            });
            return is_any_selected;
        }
        this.getSelected = function() {
            var selected_box = false;
            $.each(boxes,function(i,e){
                if (e.selected) {
                    selected_box = e;
                }
            });
            return selected_box;
        }
        this.isClosestToSelected = function(box) {
            // 1 2
            // 3 4
            // 5 6
            var closest = {
                1: [2,3],
                2: [1,4],
                3: [1,4,5],
                4: [2,3,6],
                5: [3,6],
                6: [5,4]
            };
            var is_closest = false;
            var selected_box = this.getSelected();
            /*console.log('================================================');
            console.log('selected_box = ');
            console.log(selected_box);
            console.log('selected_box.position = ' + selected_box.position);
            console.log('box.position = ' + box.position);
            console.log('closest[box.position] = ' + closest[box.position]);
            console.log($.inArray(selected_box.position,closest[box.position]));*/
            is_closest = $.inArray(selected_box.position, closest[box.position]);
            return is_closest;
        }
        this.swapPositions = function(box1, box2) {
            var b1_pos = box1.position;
            var b2_pos = box2.position;
            box1.position = b2_pos;
            box2.position = b1_pos;
            this.repaint();
            this.unselectAll();

        }
        this.unselectAll = function() {
            $.each(boxes,function(i,e){
                e.unselect();
            });
        }

    }

    // class Box
    function Box(id,color) {

        this.field;

        var html = '<div class="box" style="' +
                    'width:100px; ' +
                    'height:100px;' +
                    'background-color:'+color+'; ' +
                    'float: left; ' +
                    'border: 1px solid silver; ' +
                    'cursor: pointer; ' +
                    '" ' +
                    'data-id="'+ id +'"' +
                '></div>';
        var element = $(html);
        element.box = this;
        this.selected = false;

        this.addSelectAction = function() {
            var that = this;
            $(element).on('click',function() {
                if (that.selected) {
                    that.unselect();
                } else {
                    if (that.field.isAnyBoxSelected()) {
                        //console.log('--> selected');
                        if (that.field.isClosestToSelected(that) >= 0) {
                            that.field.swapPositions(that.field.getSelected(),that);
                            //console.log('--> closest');
                        } else {
                            that.field.unselectAll();
                        }
                    } else {
                        that.select();
                    }
                    //console.log($('.box'));
                }
            });
        };

        this.select = function() {
            this.selected = true;
            element.css('border', '3px solid red');
            element.css('width', '96px');
            element.css('height', '96px');
        }
        this.unselect = function() {
            this.selected = false;
            element.css('border', '1px solid silver');
            element.css('width', '100px');
            element.css('height', '100px');
        }

        this.getElement = function() {
            return element;
        };

        this.addSelectAction();
    }



    $(document).ready(function(){

        var field = new Field();
        field.addBox(1,'green');
        field.addBox(2,'pink');
        field.addBox(3,'yellow');
        field.addBox(4,'blue');
        field.addBox(5,'grey');
        field.addBox(6,'silver');

        field.correct_line = '1,2,3,4,5,6';

        field.repaint();
    });

</script>



<div id="yes"></div>


</body>

</html>