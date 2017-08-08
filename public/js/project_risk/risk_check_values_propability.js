/**
 * 
 */
var inputQuantity = [];
$(function() {
  $("input.propability").each(function(i) {
    inputQuantity[i]=this.defaultValue;
     $(this).data("idx",i); // save this field's index to access later
  });
  $("input.propability").on("keyup", function (e) {
    var $field = $(this),
        val=this.value,
        $thisIndex=parseInt($field.data("idx"),10); // retrieve the index
    if (this.validity && this.validity.badInput || isNaN(val) || $field.is(":invalid") ) {
        this.value = inputQuantity[$thisIndex];
        return;
    }
    if (val.length > Number($field.attr("maxlength"))) {
      val=val.slice(0, 2);
      $field.val(val);
    }
    inputQuantity[$thisIndex]=val;
  });
});