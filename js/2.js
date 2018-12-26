/**
 * window对象上挂载新对象call;
 */
(function () {
   var call={
       'data':'你好',
       'a':function () {
            alert('a');
       },
       'b':function (b) {
           alert(b)
       }
   }
   window.call=call;
})(window);
